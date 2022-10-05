<?php declare(strict_types=1);

namespace Core\Methods;

use Core\Database;
use Core\Tools\selfThrows;
use Krugozor\Database\MySqlException;

class Users
{
    /**
     * Возвращает информацию об пользователе по айди
     * @param int $aId
     * @param string $token
     * @return array
     * @throws MySqlException
     * @throws selfThrows
     */
    public function get(int $aId, string $token): array
    {
        $accountDetails = Database::getInstance()->query("SELECT * FROM users WHERE id = '?s'", $aId === 0 ? Database::getInstance()->query("SELECT aid FROM tokens WHERE token = '?s'", $token)->fetchAssoc()['aid'] : $aId);

        if (!$accountDetails->getNumRows())
            throw new selfThrows(["message" => "user not found"], 404);

        $accountDetailsAsArray = $accountDetails->fetchAssoc();

        return [
            "aid" => (int) $accountDetailsAsArray["id"],
            "username" => $accountDetailsAsArray["username"],
            "lastSeen" => (int) $accountDetailsAsArray["lastSeen"]
        ];
    }


    /**
     * Возвращает массив пользователей по поисковому запросу
     * @param string $query
     * @return array
     * @throws MySqlException
     * @throws selfThrows
     */
    public function search(string $query): array
    {
        $accountsDetails = Database::getInstance()->query("SELECT * FROM users WHERE username LIKE \"%?S%\"", $query);

        if (!$accountsDetails->getNumRows())
            throw new selfThrows(["message" => "users not found by search query"], 404);

        $accountsDetailsAsArray = $accountsDetails->fetchAssocArray();

        $preparedData = [];

        foreach ($accountsDetailsAsArray as $accountDetails) {
            $preparedData[] = [
                "aid" => (int) $accountDetails["id"],
                "username" => $accountDetails["username"],
                "lastSeen" => (int) $accountDetails["lastSeen"]
            ];
        }

        return $preparedData;
    }
}