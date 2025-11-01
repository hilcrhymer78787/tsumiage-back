<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class InvitationCreateEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    private function createLoginAndInvitedUser(): array
    {
        // 自分自身を作成 & ログイン
        $loginUser = $this->actingAsUser([
            'email' => 'loginUser@gmail.com',
            'name' => 'loginUser',
        ]);

        // 招待するユーザーを作成
        $invitedUser = User::factory()->create([
            'email' => 'invitedUser@gmail.com',
            'name' => 'invitedUser',
        ]);

        return [$loginUser, $invitedUser];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 招待を新規作成できる(): void
    {
        [$loginUser, $invitedUser] = $this->createLoginAndInvitedUser();

        $response = $this->postJson('/api/invitation/create', [
            'email' => $invitedUser->email,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => $invitedUser->name . 'さんに友達申請しました'],
            ]);

        $this->assertDatabaseHas('invitations', [
            'invitation_from_user_id' => $loginUser->id,
            'invitation_to_user_id' => $invitedUser->id,
            'invitation_status' => 1, //招待中
        ]);
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function このメールアドレスは登録されていません(): void
    {
        $this->createLoginAndInvitedUser();

        $response = $this->postJson('/api/invitation/create', [
            'email' => 'notfound@gmail.com',
        ]);

        $response->assertStatus(404)->assertJson([
            'status' => 404,
            'message' => 'このメールアドレスは登録されていません',
            'data' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 自分自身に友達申請することはできません(): void
    {
        [$loginUser,] = $this->createLoginAndInvitedUser();


        $response = $this->postJson('/api/invitation/create', [
            'email' => $loginUser->email,
        ]);

        $response->assertStatus(400)->assertJson([
            'status' => 400,
            'message' => '自分自身に友達申請することはできません',
            'data' => null,
        ]);
    }
    #[\PHPUnit\Framework\Attributes\Test]
    public function 〇〇さんにはすでに友達です(): void
    {
        [$loginUser, $invitedUser] = $this->createLoginAndInvitedUser();

        Invitation::create([
            'invitation_from_user_id' => $loginUser->id,
            'invitation_to_user_id' => $invitedUser->id,
            'invitation_status' => 2, //すでに友達
        ]);

        $response = $this->postJson('/api/invitation/create', [
            'email' => $invitedUser->email,
        ]);

        $response->assertStatus(409)->assertJson([
            'status' => 409,
            'message' => $invitedUser->name . 'さんにはすでに友達です',
            'data' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 〇〇さんにはすでに友達申請をしています(): void
    {
        [$loginUser, $invitedUser] = $this->createLoginAndInvitedUser();

        Invitation::create([
            'invitation_from_user_id' => $loginUser->id,
            'invitation_to_user_id' => $invitedUser->id,
            'invitation_status' => 1, //招待中
        ]);

        $response = $this->postJson('/api/invitation/create', [
            'email' => $invitedUser->email,
        ]);

        $response->assertStatus(409)->assertJson([
            'status' => 409,
            'message' => $invitedUser->name . 'さんにはすでに友達申請をしています',
            'data' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 〇〇さんからの友達申請が来ているため許可してください(): void
    {
        [$loginUser, $invitedUser] = $this->createLoginAndInvitedUser();

        Invitation::create([
            'invitation_from_user_id' => $invitedUser->id,
            'invitation_to_user_id' => $loginUser->id,
            'invitation_status' => 1, //招待中
        ]);

        $response = $this->postJson('/api/invitation/create', [
            'email' => $invitedUser->email,
        ]);

        $response->assertStatus(409)->assertJson([
            'status' => 409,
            'message' => $invitedUser->name . 'さんからの友達申請が来ているため許可してください',
            'data' => null,
        ]);
    }
}
