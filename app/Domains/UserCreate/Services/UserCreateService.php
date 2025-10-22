<?php

declare(strict_types=1);

namespace App\Domains\UserCreate\Services;

use App\Domains\Shared\CheckIsExistEmail\Services\CheckIsExistEmailService;
use App\Domains\Shared\LoginInfo\Entities\LoginInfoEntity;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Domains\UserCreate\Parameters\UserCreateParameter;
use App\Domains\UserCreate\Queries\UserCreateQuery;
use App\Http\Exceptions\AppHttpException;
use App\Http\Requests\UserCreateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserCreateService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
        private readonly CheckIsExistEmailService $checkIsExistEmailService,
        private readonly UserCreateQuery $query,
    ) {}

    public function upsertUser(UserCreateParameter $params, UserCreateRequest $request): LoginInfoEntity
    {
        return empty($params->id)
            ? $this->createUser($params, $request)
            : $this->updateUser($params, $request);
    }

    /**
     * 新規作成処理（JWT + メール認証対応）
     */
    private function createUser(UserCreateParameter $params, UserCreateRequest $request): LoginInfoEntity
    {
        $this->assertEmailUnique($params->email);

        // パスワードをハッシュ化して作成
        $loginInfoModel = $this->query->createUser($params);

        // メール設定がある場合のみメール認証通知を送る
        if (! empty(config('mail.mailers.smtp.username'))) {
            $loginInfoModel->sendEmailVerificationNotification();
        }

        // ファイル保存
        $this->storeUserFile($params, $request);

        Auth::login($loginInfoModel);
        $request->session()->regenerate();

        return $this->toLoginInfoEntity($loginInfoModel);
    }

    /**
     * 更新処理
     */
    private function updateUser(UserCreateParameter $params, UserCreateRequest $request): LoginInfoEntity
    {
        $loginInfoModel = $this->loginInfoService->getLoginInfo($request);
        if (! $loginInfoModel) {
            throw new AppHttpException(401, 'トークンが有効期限切れです');
        }

        $this->assertEmailUnique($params->email, $loginInfoModel->email);

        $this->query->updateUser($params, $loginInfoModel);

        if (! empty($params->password)) {
            $this->query->updatePassword($params, $loginInfoModel);
        }

        $this->storeUserFile($params, $request);

        if ($params->userImg !== $params->imgOldname) {
            Storage::delete('public/'.$params->imgOldname);
        }

        $loginInfoModel = $this->loginInfoService->getLoginInfo($request);

        return $this->toLoginInfoEntity($loginInfoModel);
    }

    /**
     * メールアドレスの重複確認
     */
    private function assertEmailUnique(string $email, ?string $currentEmail = null): void
    {
        $existEmail = $this->checkIsExistEmailService->checkIsExistEmail($email);

        // メールアドレスが存在する && 自分のではない
        if ($existEmail && $email !== $currentEmail) {
            throw new AppHttpException(409, '', ['emailError' => 'このメールアドレスは既に登録されています']);
        }
    }

    /**
     * ファイル保存処理
     */
    private function storeUserFile(UserCreateParameter $params, UserCreateRequest $request): void
    {
        if (empty($request['file'])) {
            return;
        }
        $request['file']->storeAs(
            '',                 // サブフォルダが不要なら空
            $params->userImg,
            'public'
        );
    }

    /**
     * Entity 変換
     */
    private function toLoginInfoEntity(User $loginInfoModel): LoginInfoEntity
    {
        return new LoginInfoEntity(
            id: $loginInfoModel->id,
            email: $loginInfoModel->email,
            name: $loginInfoModel->name,
            userImg: $loginInfoModel->user_img,
            emailVerifiedAt: $loginInfoModel->email_verified_at,
        );
    }
}
