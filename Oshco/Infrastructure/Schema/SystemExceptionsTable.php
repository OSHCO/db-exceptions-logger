<?php

namespace Oshco\Infrastructure\Schema;

use WebFiori\Database\Attributes\Column;
use WebFiori\Database\Attributes\Table;
use WebFiori\Database\DataType;

#[Table(name: 'system_exceptions', comment: 'Stores logged system exceptions.')]
#[Column(name: 'id', type: DataType::INT, primary: true, identity: true)]
#[Column(name: 'hash', type: DataType::NVARCHAR, size: 128)]
#[Column(name: 'date', type: DataType::DATETIME2, default: 'now')]
#[Column(name: 'code', type: DataType::INT)]
#[Column(name: 'class', type: DataType::VARCHAR, size: 128)]
#[Column(name: 'exception_class', type: DataType::VARCHAR, size: 128)]
#[Column(name: 'message', type: DataType::NVARCHAR, size: 256)]
#[Column(name: 'line', type: DataType::INT)]
#[Column(name: 'url', type: DataType::NVARCHAR, size: 256, nullable: true)]
#[Column(name: 'parameters', type: DataType::NVARCHAR, size: 1024, nullable: true)]
#[Column(name: 'trace', type: DataType::NVARCHAR, size: 1024)]
#[Column(name: 'portal_id', type: DataType::INT, nullable: true, comment: 'The portal where the exception occurred.')]
class SystemExceptionsTable {
}
