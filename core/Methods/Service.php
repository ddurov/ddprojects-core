<?php declare(strict_types=1);

namespace Core\Methods;

use Core\Database;
use Core\Tools\selfThrows;
use Krugozor\Database\MySqlException;

class Service
{

    /**
     * Возвращает обновления для выбранного продукта (Внимание, при выборке amount = last и sort = asc, возвращает последнее обновление С КОНЦА списка)
     * @param string $product
     * @param string $amount Выборка last/all
     * @param string|null $sort Выборка desc/asc
     * @return array
     * @throws MySqlException|selfThrows
     */
    public function getUpdates(string $product, string $amount, ?string $sort): array
    {

        $productUpdatesDetails = Database::getInstance()->query("SELECT * FROM updates WHERE product = '?s' ORDER BY id ?s", $product, $sort ?? "DESC");

        if (!$productUpdatesDetails->getNumRows())
            throw new selfThrows(["message" => "updates not found for this product"], 404);

        $productUpdatesDetailsAsArray = $productUpdatesDetails->fetchAssocArray();

        $preparedData = [];

        switch ($amount) {
            case "all":
                foreach ($productUpdatesDetailsAsArray as $productUpdate) {
                    $preparedData[] = [
                        "version" => $productUpdate['version'],
                        "downloadLink" => $productUpdate['downloadLink'],
                        "changelog" => $productUpdate['changelog']
                    ];
                }
                break;
            case "last":
                $preparedData = [
                    "version" => $productUpdatesDetailsAsArray[0]['version'],
                    "downloadLink" => $productUpdatesDetailsAsArray[0]['downloadLink'],
                    "changelog" => $productUpdatesDetailsAsArray[0]['changelog']
                ];
                break;
        }

        return $preparedData;

    }

    /**
     * Возвращает массив с доменами и хэшаши их сертификатов (в случае ошибки, message: domain is invalid)
     * @param string $domainList
     * @return array
     */
    public function getPinningHashDomains(string $domainList): array
    {
        $preparedData = [];

        $domainList = explode(",", $domainList);

        for ($i = 0; $i < count($domainList); $i++) {
            if (gethostbyname($domainList[$i]) === $domainList[$i]) {
                $preparedData[] = ["domain" => $domainList[$i], "requestStatus" => "error", "message" => "domain is invalid"];
                continue;
            }
            $preparedData[] = ["domain" => $domainList[$i], "requestStatus" => "ok", "hash" => shell_exec("openssl s_client -verify_quiet -connect ".htmlspecialchars($domainList[$i], ENT_QUOTES).":443 | openssl x509 -pubkey -noout | openssl pkey -pubin -outform der | openssl dgst -sha256 -binary | openssl enc -base64")];
        }

        return $preparedData;
    }
}