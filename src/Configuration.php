<?php


class Configuration
{

    /**
     * @var string
     */
    private string $databaseHost;
    
    /**
     * @var string
     */
    private string $databasePort;
    
    /**
     * @var string
     */
    private string $databaseUser;
    
    /**
     * @var string
     */
    private string $databasePassword;
    
    /**
     * @var string
     */
    private string $databaseName;
    
    /**
     * @var string
     */
    private string $databaseTable;

    /**
     * Configuration constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->databaseHost = getenv('MYSQL_HOST');
        if (false === $this->databaseHost) {
            throw new Exception('env:MYSQL_HOST not set');
        }
        $this->databasePort = getenv('MYSQL_PORT');
        if (false === $this->databasePort) {
            throw new Exception('env:MYSQL_PORT not set');
        }
        $this->databaseUser = getenv('MYSQL_USER');
        if (false === $this->databaseUser) {
            throw new Exception('env:MYSQL_USER not set');
        }
        $this->databasePassword = getenv('MYSQL_PASSWORD');
        if (false === $this->databasePassword) {
            throw new Exception('env:MYSQL_PASSWORD not set');
        }
        $this->databaseName = getenv('MYSQL_DATABASE');
        if (false === $this->databaseName) {
            throw new Exception('env:MYSQL_DATABASE not set');
        }
        $this->databaseTable = getenv('MYSQL_TABLE');
        if (false === $this->databaseTable) {
            throw new Exception('env:MYSQL_TABLE not set');
        }

    }

    /**
     * @return string
     */
    public function getDatabaseHost(): string
    {
        return $this->databaseHost;
    }

    /**
     * @return string
     */
    public function getDatabasePort(): string
    {
        return $this->databasePort;
    }

    /**
     * @return string
     */
    public function getDatabaseUser(): string
    {
        return $this->databaseUser;
    }

    /**
     * @return string
     */
    public function getDatabasePassword(): string
    {
        return $this->databasePassword;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->databaseName;
    }

    /**
     * @return string
     */
    public function getDatabaseTable(): string
    {
        return $this->databaseTable;
    }

}
