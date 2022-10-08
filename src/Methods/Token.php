<?php declare(strict_types=1);

namespace Core\Methods;

use Core\Database;
use Core\Tools\selfThrows;
use Exception;
use Krugozor\Database\MySqlException;

class Token
{
    /**
     * Возвращает новый токен
     * @param int $tokenType
     * @param string $sessionId
     * @return string
     * @throws MySqlException|Exception
     */
    public function create(int $tokenType, string $sessionId): string
    {
        $token = bin2hex(openssl_random_pseudo_bytes(48));

        Database::getInstance()
            ->query("INSERT INTO tokens (aid, token, tokenType) VALUES ((SELECT aid FROM sessions WHERE sid = '?s'), '?s', ?i)",
                $sessionId,
                $token,
                $tokenType);

        return $token;
    }

    /**
     * Возвращает 1 токен
     * @param int $tokenType
     * @param string $sessionId
     * @return string
     * @throws MySqlException
     */
    public function get(int $tokenType, string $sessionId): string
    {
        return Database::getInstance()
            ->query("SELECT token FROM tokens WHERE aid = (SELECT aid FROM sessions WHERE sid = '?s') AND tokenType = ?i", $sessionId, $tokenType)
            ->fetchAssoc()['token'];
    }

    /**
     * Возвращает true в случае успешной проверки, выбрасывает исключение если не успешно
     * @param string $token
     * @param string $sessionId
     * @return bool
     * @throws MySqlException|selfThrows
     */
    public function check(string $token, string $sessionId): bool
    {
        if (!Database::getInstance()->query("SELECT * FROM tokens WHERE aId = (SELECT aid FROM sessions WHERE sid = '?s') AND token = '?s'", $sessionId, $token)->getNumRows())
            throw new selfThrows(["message" => "token not found"]);

        return true;
    }
}