<?php

namespace App\Utils;

use PDO;

/**
 * Class Paginator
 * @package App\Utils
 */
class Paginator
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * Paginator constructor.
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $queryString
     * @param array $queryParameters
     * @param array $requestParameters
     * @return array
     */
    public function paginate(string $queryString, array $queryParameters, array $requestParameters): array
    {
        $page = $this->getPaginationParameter($requestParameters, 'page', 1);
        $limit = $this->getPaginationParameter($requestParameters, 'limit', 1);

        $totalCount = $this->getTotalCount($queryString, $queryParameters);
        $totalPageCount = (int)ceil($totalCount / $limit);

        if ($page > $totalPageCount) {
            $page = $totalPageCount;
        }

        $offset = $limit * ($page - 1);
        $stmt = $this->connection->prepare("$queryString LIMIT {$limit} OFFSET {$offset}");
        $stmt->execute($queryParameters);

        return [
            'list' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'pagination' => [
                'totalItemsCount' => $totalCount,
                'currentPage' => $page,
                'totalPagesCount' => $totalPageCount
            ]
        ];
    }

    /**
     * @param string $queryString
     * @param array $queryParameters
     * @return int
     */
    private function getTotalCount(string $queryString, array $queryParameters): int
    {
        $explodedQueryString = explode('FROM', $queryString);
        $totalCountQuery = 'SELECT COUNT(*) FROM'.$explodedQueryString[1];

        $stmt = $this->connection->prepare($totalCountQuery);
        $stmt->execute($queryParameters);

        return (int)$stmt->fetchColumn();
    }

    /**
     * @param array $requestParameters
     * @param string $parameterName
     * @param int $defaultValue
     * @return int
     */
    private function getPaginationParameter(array $requestParameters, string $parameterName, int $defaultValue = 1): int
    {
        $parameter = (int)($requestParameters[$parameterName] ?? $defaultValue);

        return $parameter < 1 ? $defaultValue : $parameter;
    }
}