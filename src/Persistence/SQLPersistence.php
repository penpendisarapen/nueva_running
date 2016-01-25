<?php


namespace Mavericks\Persistence;

use PDO;
use Mavericks\Data\CurrentSeason;

class SQLPersistence
{
  const HOST = 'localhost';
  const USERNAME = 'nueva';
  const DBNAME = 'nueva';
  const PASSWORD = 'bbq922nv';

  /**
   * @var
   */
  protected $dbh;

  /**
   * @var CurrentSeason
   */
  protected $CurrentSeason;

  public function __construct(CurrentSeason $CurrentSeason)
  {
    $this->CurrentSeason = $CurrentSeason;
  }

  /**
   * @return PDO
   */
  protected function getDBHandle()
  {
    if (!$this->dbh)
    {
      $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DBNAME;
      $options = array (
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
      );

      $this->dbh = new PDO($dsn, self::USERNAME, self::PASSWORD, $options);
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
  protected function fetch($sql, $params)
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
    $statement = $this->query($sql, $params);

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