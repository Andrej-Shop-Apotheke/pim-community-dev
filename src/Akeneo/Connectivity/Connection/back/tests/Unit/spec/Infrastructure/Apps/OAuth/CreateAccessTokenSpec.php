<?php

declare(strict_types=1);

namespace spec\Akeneo\Connectivity\Connection\Infrastructure\Apps\OAuth;

use Akeneo\Connectivity\Connection\Application\Apps\Service\CreateAccessTokenInterface;
use Akeneo\Connectivity\Connection\Infrastructure\Apps\OAuth\ClientProviderInterface;
use Akeneo\Connectivity\Connection\Infrastructure\Apps\OAuth\CreateAccessToken;
use Akeneo\Connectivity\Connection\Infrastructure\Apps\OAuth\RandomCodeGeneratorInterface;
use Akeneo\Tool\Bundle\ApiBundle\Entity\Client;
use OAuth2\IOAuth2GrantCode;
use OAuth2\Model\IOAuth2AuthCode;
use PhpSpec\ObjectBehavior;

class CreateAccessTokenSpec extends ObjectBehavior
{
    public function let(
        IOAuth2GrantCode $storage,
        ClientProviderInterface $clientProvider,
        RandomCodeGeneratorInterface $randomCodeGenerator
    ): void {
        $this->beConstructedWith($storage, $clientProvider, $randomCodeGenerator);
    }

    public function it_is_a_create_access_token(): void
    {
        $this->shouldHaveType(CreateAccessToken::class);
        $this->shouldImplement(CreateAccessTokenInterface::class);
    }

    public function it_creates_an_access_token(
        IOAuth2GrantCode $storage,
        Client $client,
        IOAuth2AuthCode $authCode,
        ClientProviderInterface $clientProvider,
        RandomCodeGeneratorInterface $randomCodeGenerator
    ): void {
        $clientProvider->findClientByAppId('client_id_1234')->willReturn($client);
        $storage->getAuthCode('auth_code_1234')->willReturn($authCode);
        $randomCodeGenerator->generate()->willReturn('generated_token_123');

        $authCode->getData()->willReturn([]);
        $authCode->getScope()->willReturn('delete_products');
        $token = [
            'access_token' => 'generated_token_123',
            'token_type' => 'bearer',
        ];

        $storage->createAccessToken(
            'generated_token_123',
            $client,
            [],
            null
        )->shouldBeCalled();
        $storage->markAuthCodeAsUsed('auth_code_1234')->shouldBeCalled();

        $this->create('client_id_1234', 'auth_code_1234')->shouldReturn($token);
    }

    public function it_processes_only_valid_client(ClientProviderInterface $clientProvider): void
    {
        $clientProvider->findClientByAppId('client_id_1234')->willReturn(null);
        $this
            ->shouldThrow(
                new \InvalidArgumentException('No client found with the given client id.')
            )
            ->during('create', ['client_id_1234', 'auth_code_1234']);
    }

    public function it_processes_only_valid_auth_code(
        IOAuth2GrantCode $storage,
        Client $client,
        ClientProviderInterface $clientProvider
    ): void {
        $clientProvider->findClientByAppId('client_id_1234')->willReturn($client);
        $storage->getAuthCode('auth_code_1234')->willReturn(null);

        $this
            ->shouldThrow(
                new \InvalidArgumentException('Unknown authorization code.')
            )
            ->during('create', ['client_id_1234', 'auth_code_1234']);
    }
}