<?php

namespace ConnectionsBaseDir;
use DB;
/**
 * Trait MysqlDB
 * @package ConnectionsBaseDir
 */
trait MysqlDB
{
    public $db = 'mysql';
    private static $table_name = null;

    /** @var string $where string contendo filtros WHERE em query */
    private static $where;

    /** @var string $options string contendo opcoes de ordem e limit em query */
    private static $options;

    private static $database_structure = [
        'id int(11) NOT NULL AUTO_INCREMENT',
        "data_created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'",
        "updated_in timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP",
        "incidents int(2) NOT NULL DEFAULT 1",
        'solved int(1) NOT NULL DEFAULT 0',
        "level int(1) NOT NULL DEFAULT 0",
        'log_name varchar(50) NOT NULL',
        "identifier varchar(50) NOT NULL DEFAULT 'none'",
        'content mediumtext NOT NULL',
        "notification_sent tinyint(1) NOT NULL DEFAULT 0",
        "PRIMARY KEY (id)"
    ];

    /**
     * Set Table to future query
     * @param $table string table name
     */
    public static function defineTable($table)
    {
        self::$table_name = $table;
    }

    /**
     * INSERT query
     * @param  array  $content array onde keys são os campos da tabela
     *  e values as informações que serão inseridas
     */
    public static function insert(array $content)
    {
        self::validateTable();

        $keys = self::getKeysSQLFormated($content);
        $values = self::getValuesSQLFormated($content);

        $query = 'INSERT INTO '.self::$table_name.'('.$keys.') VALUES('.$values.')';

        return DB::insert($query);
    }

    /**
     * SELECT query
     * @param  array $filter contem filtro WHERE
     * @param  array  $dados campos que devem ser selecionados em query
     */
    public static function select(array $filter, array $dados = null)
    {
        self::validateTable();

        $campos = '*';
        $where = '';

        if (is_null($dados) == false) {
            $campos = self::getValuesSQLFormated($dados, true);
        }

        $query = 'SELECT '.$campos.' FROM '.self::$table_name;

        self::setOptions($filter);

        if (empty(self::$where) == false) {
            $query .= ' WHERE '.self::$where;
        }

        $query .= self::$options;

        return self::fetchResult(DB::select($query));
    }

    /**
     * Set Options as filters to query
     * @param $filter
     */
    private static function setOptions($filter)
    {
        if (isset($filter['order']) && isset($filter['limit'])) {
            self::$options = ' order by data_created ' . $filter['order'] . ' limit ' . $filter['limit'];
            unset($filter['limit'], $filter['order']);
        }

        if (empty($filter)) {
            return;
        }

        $string = '';

        foreach ($filter as $key => $value) {
            $string .= ($key == 'level' ? "$key = $value and " : "$key = '$value' and ");
        }

        self::$where = substr($string, 0, -4);
    }

    /**
     * UPDATE query
     * @param  array  $content  string contendo campos e valores que serao atualizados
     * @param  string $filter string com filtro para atualização
     */
    public static function updateData($dados, $filter)
    {
        self::validateTable();

        $dados = key($dados).' = '.$dados[key($dados)];
        $filter = key($filter).' = '.$filter[key($filter)];

        return DB::update('UPDATE ' . self::$table_name . ' SET ' . $dados . ' WHERE ' . $filter);
    }

    /**
     * Verify if has result on select query
     * @param $result_query
     * @return bool
     */
    private static function fetchResult($result_query)
    {
        if (count($result_query) < 1) {
            return false;
        }

        return $result_query;
    }

    /**
     * Cria formatação de query utilizando keys de array
     * como valores de campos de tabela do banco
     * @param  array  $keys array contendo campos e valores do banco
     * @return string retorna string com formatação para manipulação no banco
     */
    private static function getKeysSQLFormated(array $keys)
    {
        return implode(', ',array_keys($keys));
    }

    /**
     * Cria formatação de query utilizando values de array
     * como valores a serem inseridos ou atualizados no banco
     * @param  array  $values [description]
     * @return [type]         [description]
     */
    private static function getValuesSQLFormated(array $values, $sem_aspas_simples = false)
    {
        if ($sem_aspas_simples) {
            return implode(", ", array_values($values));
        }

        return "'".implode("', '", array_values($values))."'";
    }

    /**
     * Validate if table exists, if not, create it
     * @return bool return true if table exists or table ir created
     */
    private static function validateTable()
    {
        if (self::tableExists()) {
            return true;
        }

        if (self::createTable()) {
            return true;
        }
    }

    /**
     * Verifica se tabela existe no banco.
     *
     * @return boolean retorna true caso exista, falso caso contrario
     */
    private static function tableExists()
    {
        return (count(DB::select("SHOW TABLES LIKE '".self::$table_name."'")) > 0) ? true : false;
    }

    /**
     * Cria tabela no banco com as colunas necessarias.
     *
     * @return boolean retorna true caso tenha conseguido criar, false caso contrario
     */
    private static function createTable()
    {
        $database_structure = implode(', ', self::$database_structure);
        return DB::statement("CREATE TABLE ".self::$table_name."($database_structure)");
    }

    private function log_error()
    {
        die();
        // CRIAR LOG DE ERRO AO INSERIR LOG
    }

}
