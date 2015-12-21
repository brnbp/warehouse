<?php

namespace App\Storage;

use Illuminate\Support\Facades\DB;

class MysqlStorage implements StorageDriverInterface
{
    private $table_name;

    /** @var string $where string contendo filtros WHERE em query */
    private $where;

    /** @var string $options string contendo opcoes de ordem e limit em query */
    private $options;

    private $database_structure = [
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
        "INDEX verify_existence (identifier, log_name, content(10000), level)",
        "PRIMARY KEY (id)"
    ];

    /**
     * Set Table to future query
     * @param $table string table name
     */
    public function defineTable($table)
    {
        $this->table_name = $table;
    }

    /**
     * INSERT query
     * @param  array  $content array onde keys são os campos da tabela
     *  e values as informações que serão inseridas
     */
    public function insert(array $content)
    {
        $this->validateTable();

        $keys = $this->getKeysSQLFormated($content);
        $values = $this->getValuesSQLFormated($content);

        $query = 'INSERT INTO '.$this->table_name.'('.$keys.') VALUES('.$values.')';

        return DB::insert($query);
    }

    /**
     * SELECT query
     * @param  array $filter contem filtro WHERE
     * @param  array  $dados campos que devem ser selecionados em query
     * @return
     */
    public function select(array $filter, array $dados = null)
    {
        $this->validateTable();

        $campos = '*';
        $where = '';

        if (is_null($dados) == false) {
            $campos = $this->getValuesSQLFormated($dados, true);
        }

        $query = 'SELECT '.$campos.' FROM '.$this->table_name;

        $this->setOptions($filter);

        if (empty($this->where) == false) {
            $query .= ' WHERE '.$this->where;
        }

        $query .= $this->options;

        return $this->fetchResult(DB::select($query));
    }

    /**
     * Set Options as filters to query
     * @param $filter
     */
    private function setOptions($filter)
    {
        if (isset($filter['order']) && isset($filter['limit'])) {
            $this->options = ' order by data_created ' . $filter['order'] . ' limit ' . $filter['limit'];
            unset($filter['limit'], $filter['order']);
        }

        if (empty($filter)) {
            return;
        }

        $string = '';

        foreach ($filter as $key => $value) {
            $string .= ($key == 'level' ? "$key = $value and " : "$key = '$value' and ");
        }

        $this->where = substr($string, 0, -4);
    }

    /**
     * UPDATE query
     * @param  array  $content  string contendo campos e valores que serao atualizados
     * @param  string $filter string com filtro para atualização
     */
    public function updateData($dados, $filter)
    {
        $this->validateTable();

        $dados = key($dados).' = '.$dados[key($dados)];
        $filter = key($filter).' = '.$filter[key($filter)];

        return DB::update('UPDATE ' . $this->table_name . ' SET ' . $dados . ' WHERE ' . $filter);
    }

    /**
     * Verify if has result on select query
     * @param $result_query
     * @return bool
     */
    private function fetchResult($result_query)
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
    private function getKeysSQLFormated(array $keys)
    {
        return implode(', ',array_keys($keys));
    }

    /**
     * Cria formatação de query utilizando values de array
     * como valores a serem inseridos ou atualizados no banco
     * @param  array  $values [description]
     * @return [type]         [description]
     */
    private function getValuesSQLFormated(array $values, $sem_aspas_simples = false)
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
    private function validateTable()
    {
        if ($this->tableExists()) {
            return true;
        }

        if ($this->createTable()) {
            return true;
        }
    }

    /**
     * Verifica se tabela existe no banco.
     *
     * @return boolean retorna true caso exista, falso caso contrario
     */
    private function tableExists()
    {
        return (count(DB::select("SHOW TABLES LIKE '".$this->table_name."'")) > 0) ? true : false;
    }

    /**
     * Cria tabela no banco com as colunas necessarias.
     *
     * @return boolean retorna true caso tenha conseguido criar, false caso contrario
     */
    private function createTable()
    {
        $database_structure = implode(', ', $this->database_structure);
        return DB::statement("CREATE TABLE ".$this->table_name."($database_structure)");
    }

    private function log_error()
    {
        die();
        // CRIAR LOG DE ERRO AO INSERIR LOG
    }

}