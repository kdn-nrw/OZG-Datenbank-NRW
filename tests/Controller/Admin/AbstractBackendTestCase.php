<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Tests\Controller\AbstractWebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Functional test for the controllers defined inside frontend admin controllers.
 *
 * See https://symfony.com/doc/current/book/testing.html#functional-tests
 *
 * Execute the application tests using this command (requires PHPUnit to be installed):
 *
 *     $ ./vendor/bin/phpunit
 */
abstract class AbstractBackendTestCase extends AbstractWebTestCase implements BackendTestInterface
{

    abstract protected function getRoutePrefix(): string;

    protected function getContextPrefix(): string
    {
        return self::BACKEND_URL_PREFIX;
    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    protected function getRouteUrl(string $view, array $params = []): string
    {
        $route = $this->getRoutePrefix();
        if ($view !== 'index') {
            if (array_key_exists('id', $params)) {
                $route .= '/' . $params['id'];
            }
            $route .= '/' . $view;
        }
        return $this->getContextPrefix() . $route;
    }

    protected function findUserByEmail(string $email): ?User
    {

        $managerRegistry = static::$container->get('doctrine');
        $userRepository = $managerRegistry->getRepository(User::class);

        // retrieve the test user
        return $userRepository->findOneByEmail($email);
    }

    protected function logIn(KernelBrowser $client, string $email = self::LOGIN_USERNAME): ?User
    {
        $session = self::$container->get('session');
        // retrieve the test user
        $user = $this->findUserByEmail($email);
        self::assertNotNull($user, 'The user ' . $email . ' must not be null');
        $firewallName = self::FIREWALL_NAME;
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = self::FIREWALL_CONTEXT;

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        return $user;
    }
}