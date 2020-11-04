<?php

namespace ptk\db;

/**
 * Conecta a um banco de dados.
 * 
 * @staticvar |PDO $pdo
 * @param string $dsn
 * @param string|null $username
 * @param string|null $passwd
 * @param bool $reset Se TRUE, força a reconexão.
 * @return \PDO
 */
function connect(string $dsn, ?string $username = null, ?string $passwd = null, bool $reset = false): \PDO {
    static $pdo = null;
    
    if($reset === false){
        if(!is_null($pdo)){
            return $pdo;
        }
    }
    
    try{
        $pdo = new \PDO($dsn, $username, $passwd);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch (\Exception $ex) {
        trigger_error($ex->getMessage(), \E_USER_ERROR);
    }
    
    return $pdo;
}

/**
 * Executa uma query e retorna o seu resultado.
 * 
 * @param string $statement
 * @param array $context
 * @return array
 */
function query(string $statement, array $context = []): array {
    $pdo = connect('');
    try{
        $sth = $pdo->prepare($statement);
        $sth->execute($context);
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $ex) {
        trigger_error($ex->getMessage(), \E_USER_ERROR);
    }
}

/**
 * Executa uma alteração no banco de dados através de transação.
 * 
 * @param string $statement
 * @param array $context
 * @return void
 */
function transaction(string $statement, array $context = []): void {
    $pdo = connect('');
    try{
        if($pdo->inTransaction() === false){
            $pdo->beginTransaction();
        }
        $sth = $pdo->prepare($statement);
        $sth->execute($context);
    } catch (\Exception $ex) {
        trigger_error($ex->getMessage(), \E_USER_ERROR);
    }
}

/**
 * Finaliza uma transação aberta em transaction().
 * 
 * @return void
 */
function commit(): void {
    $pdo = connect('');
    try{
        if($pdo->inTransaction() === false){
            trigger_error("Nenhuma transação ativa neste momento.", E_USER_WARNING);
            return;
        }
        $sth = $pdo->commit();
    } catch (\Exception $ex) {
        $pdo->rollBack();
        trigger_error($ex->getMessage(), \E_USER_WARNING);
    }
}