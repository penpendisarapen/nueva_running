<?php


namespace Mavericks\Persistence;

use PDO;
use Mavericks\Data\CurrentSeason;
use Silex\Application;

class SQLPersistence
{
  /**
   * @var
   */
  protected $dbh;

  /**
   * @var Application
   */
  private $App;

  /**
   * @var CurrentSeason
   */
  protected $CurrentSeason;

  public function __construct(Application $App)
  {
    $this->App           = $App;
    $this->CurrentSeason = $App['service.currentSeason'];
  }

  /**
   * @return PDO
   */
  protected function getDBHandle()
  {
    if (!$this->dbh)
    {
      $dbConfig = $this->App['config']['db'];
      $dsn      = 'mysql:host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['dbname'];
      $options  = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
      );

      $this->dbh = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
    }

    return $this->dbh;
  }

  /**
   * @param $sql
   * @param $params
   * @return \PDOStatement
   */
  protected function query($sql, $params)
  {
    $dbh       = $this->getDBHandle();
    $statement = $dbh->prepare($sql);
    $statement->execute($params);

    return $statement;
  }

  /**
   * @param $sql
   * @param $params
   * @return array
   */
  protected function fetch($sql, $params = array())
  {
    $statement = $this->query($sql, $params);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * @param $sql
   * @param $params
   * @return string
   */
  protected function insert($sql, $params)
  {
    $this->query($sql, $params);

    return $this->getDBHandle()->lastInsertId();
  }

  /**
   * @param $sql
   * @param $params
   * @return int
   */
  protected function update($sql, $params)
  {
    $statement = $this->query($sql, $params);

    return $statement->rowCount();
  }
}