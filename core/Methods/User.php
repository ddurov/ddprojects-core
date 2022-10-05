<?php declare(strict_types=1);

namespace Core\Methods;

use Core\Database;
use Core\Tools\selfThrows;
use Exception;
use Krugozor\Database\MySqlException;

class User
{
    /**
     * Регистрирует пользователя, возвращает токен + сессию
     * @param string $login
     * @param string $password
     * @param string|null $username
     * @param string $email
     * @param string|null $emailCode
     * @param string|null $hash
     * @return array
     * @throws selfThrows|MySqlException|Exception
     */
    public function registerAccount(string $login, string $password, ?string $username, string $email, ?string $emailCode, ?string $hash): array
    {
        if (Database::getInstance()->query("SELECT * FROM users WHERE login = '?s'", $login)->getNumRows())
            throw new selfThrows(["message" => "user with provided login already registered"], 409);

        if ($username !== null)
            if (Database::getInstance()->query("SELECT * FROM users WHERE username = '?s'", $username)->getNumRows())
                throw new selfThrows(["message" => "user with provided username already registered"], 409);

        if ($emailCode === null && $hash === null) {
            return ["hash" => (new Email())->createCode($email)];
        }

        (new Email())->confirmCode($emailCode, $hash);

        $passwordSalt = bin2hex(openssl_random_pseudo_bytes(16));

        Database::getInstance()->query("INSERT INTO users (login, passwordHash, passwordSalt, email) VALUES ('?s', '?s', '?s', '?s')",
            $login,
            md5($password . $passwordSalt),
            $passwordSalt,
            $email);

        $aId = (int)Database::getInstance()->query("SELECT id FROM users WHERE login = '?s'", $login)->fetchAssoc()['id'];
        $sessionId = (new Session())->create($aId);

        Database::getInstance()->query("UPDATE users SET username = '?s' WHERE id = ?i", $username ?? "aid{$aId}", $aId);

        return ["token" => (new Token)->create(0, $sessionId), "sessionId" => $sessionId];

    }

    /**
     * Авторизует аккаунт, возвращает токен + сессию (предпочтительно уже существующую для типа девайса)
     * * TODO: Перехуярить логику блокировок в админ функции и переписать проверку бана
     * @param string $login
     * @param string $password
     * @return array
     * @throws selfThrows|MySqlException|Exception
     */
    public function auth(string $login, string $password): array
    {
        $account = Database::getInstance()->query("SELECT * FROM users WHERE login = '?s'", $login);

        if (!$account->getNumRows()) throw new selfThrows(["message" => "user with provided login not found"], 404);

        $accountAsArray = $account->fetchAssoc();

        if (md5($password . $accountAsArray['passwordSalt']) !== $accountAsArray['passwordHash']) throw new selfThrows(["message" => "invalid password"], 400);

        /*
        $ban = Database::getInstance()->query("SELECT * FROM general.bans WHERE eid = ?i", $accountAsArray['id']);
        $banAsArray = $ban->fetchAssoc();

        if ($ban->getNumRows()) throw new selfThrows(["message" => "account has been banned", "details" => ["reason" => $banAsArray["reason"], "canRestoreAccount" => (time() > $banAsArray['unbanTime'])]], 500);

        if (Database::getInstance()->query("SELECT * FROM general.attempts_auth WHERE login = '?s'", $login)->getNumRows() >= 5) {

            $banAsArray = Database::getInstance()->query("SELECT * FROM general.bans WHERE eid = ?i", $accountAsArray['id'])->fetchAssoc();
            throw new selfThrows(["message" => "account has been banned", "details" => ["reason" => $banAsArray["reason"], "canRestoreAccount" => (time() > $banAsArray['unbanTime'])]], 500);

        }
        */

        $sessionId = (new Session())->create((int) $accountAsArray['id']);
        Database::getInstance()->query("INSERT INTO auth_attempts (aid, `time`, authIp) VALUES (?i, ?i, '?s')",
            $accountAsArray['id'],
            time(),
            $_SERVER['REMOTE_ADDR']);

        return ["token" => (new Token)->get(0, $sessionId), "sessionId" => $sessionId];

    }

    /**
     * Изменяет пароль аккаунта, возвращает новый токен
     * * TODO: Сделать удаление всех авторизованных сессий кроме текущей
     * @param string $emailCode
     * @param string $hash
     * @param string $sessionId
     * @param string $newPassword
     * @return string
     * @throws selfThrows|MySqlException|Exception
     */
    public function resetPassword(string $emailCode, string $hash, string $sessionId, string $newPassword): string
    {

        (new Email())->confirmCode($emailCode, $hash, 1);

        $salt = bin2hex(openssl_random_pseudo_bytes(16));

        Database::getInstance()->query("UPDATE users SET passwordHash = '?s', passwordSalt = '?s' WHERE id = (SELECT aid FROM sessions WHERE id = '?s')",
            md5($newPassword . $salt),
            $salt,
            $sessionId);

        return (new Token)->create(0, $sessionId);

    }

    // TODO: Вернуть метод setOnline в ddMessager (с исправлениями автостатуса (был недавно/(в/на) это(м/й) неделе/месяце)) и changeName починить
    ///**
    // * @param string $token
    // * @return string
    // * @throws MySqlException
    // */
    //public static function setOnline(string $token): string
    //{

    //    Database::getInstance()->query("UPDATE messager.users SET lastSeen = 1, lastSentOnline = ?i WHERE id = ?i",
    //        time(),
    //        Database::getInstance()->query("SELECT eid FROM messager.tokens WHERE token = '?s'", $token)->fetchAssoc()['eid']);

    //    return (new Response)
    //        ->setStatus("ok")
    //        ->toJson();

    //}

    ///**
    // * @param string $newName
    // * @param string $email
    // * @param string $codeEmail
    // * @param string $hashCode
    // * @return string
    // * @throws selfThrows|MySqlException
    // */
    //public static function changeName(string $newName, string $email, string $codeEmail, string $hashCode): string
    //{

    //    $getCodeEmailStatus = json_decode(Email::confirmCode($email, $codeEmail, $hashCode), true);

    //    if ($getCodeEmailStatus['code'] !== 200) throw new selfThrows(["message" => $getCodeEmailStatus['response']['error']], $getCodeEmailStatus['code']);

    //    if (preg_match("/^e?id+\d+/u", $newName)) throw new selfThrows(["message" => "newName cannot contain the prefix eid or id"], 400);

    //    Database::getInstance()->query("DELETE FROM messager.email_codes WHERE email = '?s'", $email);
    //    Database::getInstance()->query("UPDATE messager.users SET username = '?s' WHERE email = '?s'", $newName, $email);
    //    return (new Response)
    //        ->setStatus("ok")
    //        ->toJson();

    //}

}