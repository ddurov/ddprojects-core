<?php declare(strict_types=1);

namespace Core\Methods;

use Core\Database;
use Core\Tools\selfThrows;
use Krugozor\Database\MySqlException;
use Mobile_Detect;

class Session
{
    /**
     * Возвращает новую сессию
     * @param int $aId
     * @return string
     * @throws MySqlException
     */
    public function create(int $aId): string
    {
        $sessionId = bin2hex(openssl_random_pseudo_bytes(32));

        Database::getInstance()
            ->query("INSERT INTO sessions (sid, aid, authTime, authDeviceType, authIp) VALUES ('?s', '?s', ?i, ?i, '?s')",
                $sessionId,
                $aId,
                time(),
                ((new Mobile_Detect)->isMobile() || (new Mobile_Detect)->isTablet()) ? 1 : 0,
                $_SERVER['REMOTE_ADDR']
            );

        return $sessionId;
    }

    /**
     * Возвращает 1 сессию
     * @param int $aId
     * @return string
     * @throws MySqlException
     */
    public function get(int $aId): string
    {
        return Database::getInstance()
            ->query("SELECT sid FROM sessions WHERE aid = ?i", $aId)
            ->fetchAssoc()['sid'];
    }

    /**
     * Возвращает true в случае успешной проверки, выбрасывает исключение если не успешно
     * @param string $sessionId
     * @return bool
     * @throws MySqlException|selfThrows
     */
    public function check(string $sessionId): bool
    {
        if (!Database::getInstance()->query("SELECT * FROM sessions WHERE sid = '?s'", $sessionId)->getNumRows())
            throw new selfThrows(["message" => "session not found"]);

        return true;
    }
}