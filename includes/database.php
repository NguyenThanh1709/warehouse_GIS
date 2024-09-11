<?php

function query($sql, $data = [], $stmtStatus = false)
{
  global $conn;
  $query = false;
  try {
    $stmt = $conn->prepare($sql);
    if (!empty($data)) {
      $query = $stmt->execute($data);
    } else {
      $query = $stmt->execute();
    }
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
  if ($stmtStatus && $query) {
    return $stmt;
  }

  return $query;
}

function insert($table, $dataInsert)
{
  $keys = array_keys($dataInsert);
  $field = implode(', ', $keys);
  $value = ":" . implode(', :', $keys);
  $sql = "INSERT INTO `$table` ($field) VALUES ($value)";
  // die($sql);
  return query($sql, $dataInsert);
}

function update($table, $dataUpdate, $condition = '')
{ //condition là mệnh đề sau WHERE
  $updateStr = "";
  foreach ($dataUpdate as $key => $value) {
    $updateStr .= $key . "=:" . $key . ", ";
  }
  $updateStr = rtrim($updateStr, ', ');

  if (empty($condition)) {
    $sql = "UPDATE $table SET $updateStr";
  } else {
    $sql = "UPDATE $table SET $updateStr WHERE $condition";
  }
  // die($sql);
  return query($sql, $dataUpdate);
}

function delete($table, $condition = '')
{
  if (empty($condition)) {
    $sql = "DELETE FROM $table";
  } else {
    $sql = "DELETE FROM $table WHERE $condition";
  }
  // echo $sql;
  return query($sql);
}

//Lấy dữ liệu từ câu lệnh sql
function getRaw($sql)
{
  $stmt = query($sql, [], true);
  // var_dump($stmt);
  if (is_object($stmt)) {
    $dataFetch = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dataFetch;
  }
  return false;
}

//Lấy dữ liệu từ câu lệnh sql
function firstRaw($sql)
{
  $stmt = query($sql, [], true);
  // var_dump($stmt);
  if (is_object($stmt)) {
    $dataFetch = $stmt->fetch(PDO::FETCH_ASSOC);
    return $dataFetch;
  }
  return false;
}

function get($table, $field = '*', $condition = '')
{
  $sql = "SELECT $field FROM `$table`";
  if (!empty($condition)) {
    $sql .= ' WHERE ' . $condition;
  }
  return getRaw($sql);
}

function getRows($sql)
{
  $stmt = query($sql, [], true);
  // print_r($stmt);
  if (!empty($stmt)) {
    return $stmt->rowCount();
  }
}

function insertID()
{
  global $conn;
  return $conn->lastInsertID();
}
