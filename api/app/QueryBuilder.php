<?php

namespace App;

use Pimple\Container;

class QueryBuilder extends Container {

    private array $initial_state = [ 'conditions' => [],
                                     'order_by'   => [],
                                     'limit'      => 0,
                                     'offset'     => 0 ];

    public function __construct(array $values = []) {
        $values = array_merge( $this->initial_state, $values );
        parent::__construct( $values );
    }

    public function addCondition(string $field, string $operator, string|null $value) {
        $this->offsetSet( 'conditions', array_merge(
            $this->raw( 'conditions' ),
            [ [ $field, $operator, $value ] ]
        ) );
    }

    public function selectStatement(string $table = '', array $fields = []): string {
        if( strlen( $table ) === 0 ) $table = $this->raw( 'table' );
        if( count( $fields ) === 0 ) $fields = $this->raw( 'fields' );
        $s = 'select ';
        if( $q = $this->buildSelect( $fields ) ) {
            $s .= $q;
        } else {
            $s .= '*';
        }
        $s .= ' from ' . $table;
        if( $q = $this->buildWhere() ) {
            $s .= ' where ' . $q;
        }
        if( $q = $this->buildOrderBy() ) {
            $s .= ' order by ' . $q;
        }
        if( $q = $this->buildLimit() ) {
            $s .= ' limit ' . $q;
        }
        if( $q = $this->buildOffset() ) {
            $s .= ' offset ' . $q;
        }

        return $s;
    }

    private function buildSelect(array $fields = []): ?string {
        $s = '';
        if( count( $fields ) === 0 ) $fields = $this->raw( 'fields' );
        foreach( $fields as $field_name => $field_settings ) {
            if( is_int( $field_name ) && is_string( $field_settings ) ) {
                $field_name = $field_settings;
                $field_settings = [ 'type' => null ];
            }

            match ( $field_settings['type'] ) {
                'Expression' => ( isset( $field_settings['select'] ) )
                    ? $s .= $field_settings['select'] . ' as ' . $field_name . ', '
                    : null,
                default => $s .= $field_name . ', '
            };
        }

        return strlen( $s )
            ? rtrim( $s, ', ' )
            : null;
    }

    private function buildWhere(string $op = 'and', $ctx = null): ?string {
        $s = '';
        if( is_null( $ctx ) ) {
            $ctx = $this->raw( 'conditions' );
        }
        foreach( $ctx as $condition ) {
            if( !is_array( $condition ) || count( $condition ) != 3 ) continue;
            if( is_array( $condition[0] ) ) {
                $s .= '( ' . $this->buildWhere( 'or', $condition ) . ' )';
            }
            if( is_null( $condition[2] ) ) {
                if( $condition[1] === '=' ) {
                    $s .= $condition[0] . ' is null';
                } else {
                    $s .= $condition[0] . ' not is null';
                }
            } else {
                $s .= $condition[0] . ' ' . $condition[1] . ' ' . $this->raw( 'pdo' )()->quote( $condition[2] );
            }
            $s .= ' ' . $op . ' ';
        }

        return ( strlen( $s ) )
            ? rtrim( $s, ' ' . $op . ' ' )
            : null;
    }

    private function buildOrderBy(string $op = ', ', $ctx = null): ?string {
        $s = '';
        if( is_null( $ctx ) ) {
            $ctx = $this->raw( 'order_by' );
        }
        foreach( $ctx as $key => $order ) {
            $s .= $this->raw( 'pdo' )()->quote( $key ) . ' ' . $order;
            $s .= $op;
        }

        return ( strlen( $s ) )
            ? rtrim( $s, ' ' . $op . ' ' )
            : null;
    }

    private function buildLimit(): ?string {
        return $this->raw( 'limit' );
    }

    private function buildOffset(): ?string {
        return $this->raw( 'offset' );
    }

    public function orderBy(array $order) {
        $this->offsetSet( 'order_by', $order );
    }

    public function limit(int|null $limit = 10, int|null $offset = 0) {
        $this->offsetSet( 'limit', $limit );
        $this->offset( $offset );
    }

    public function offset(int|null $offset = 0) {
        $this->offsetSet( 'offset', $offset );
    }

    public function reset() {
        foreach( $this->initial_state as $offset => $value ) {
            $this->offsetSet( $offset, $value );
        }
    }

    public function insertStatement(array $data, string $table = '', array $fields = []): string {
        if( strlen( $table ) === 0 ) $table = $this->raw( 'table' );
        if( count( $fields ) === 0 ) $fields = $this->raw( 'fields' );
        $used_fields = array_filter( $fields, fn($v, $k) => in_array( $k, array_keys( $data ) ), ARRAY_FILTER_USE_BOTH );
        $s = 'insert into ' . $table;
        if( $q = $this->buildColumns( $used_fields ) ) {
            $s .= ' ( ' . $q . ' )';
        }
        if( $q = $this->buildValues( $used_fields ) ) {
            $s .= ' values ( ' . $q . ' ) ';
        }

        return $s;
    }

    public function updateStatement(array $data, string $table = '', array $fields = []): string {
        if( strlen( $table ) === 0 ) $table = $this->raw( 'table' );
        if( count( $fields ) === 0 ) $fields = $this->raw( 'fields' );
        $used_fields = array_filter( $fields, fn($v, $k) => in_array( $k, array_keys( $data ) ), ARRAY_FILTER_USE_BOTH );
        $s = 'update ' . $table;
        if( $q = $this->buildSetData( $used_fields ) ) {
            $s .= ' set ' . $q;
        }
        if( $q = $this->buildWhere() ) {
            $s .= ' where '.$q;
        }
        return $s;
    }

    private function buildSetData(array $fields = []): ?string {
        $s = '';
        if( count( $fields ) === 0 ) $fields = $this->raw( 'fields' );
        foreach( $fields as $field_name => $field_settings ) {
            match ( $field_settings['type'] ) {
                'Expression' => null,
                default => $s .= $field_name . ' = :' . $field_name . ', '
            };
        }

        return strlen( $s )
            ? rtrim( $s, ', ' )
            : null;
    }

    private function buildColumns(array $fields = []): ?string {
        $s = '';
        if( count( $fields ) === 0 ) $fields = $this->raw( 'fields' );
        foreach( $fields as $field_name => $field_settings ) {
            match ( $field_settings['type'] ) {
                'Expression' => null,
                default => $s .= $field_name . ', '
            };
        }

        return strlen( $s )
            ? rtrim( $s, ', ' )
            : null;
    }

    private function buildValues(array $fields): ?string {
        $s = '';
        if( count( $fields ) === 0 ) $fields = $this->raw( 'fields' );
        foreach( $fields as $field_name => $field_settings ) {
            match ( $field_settings['type'] ) {
                'Expression' => null,
                default => $s .= ':' . $field_name . ', '
            };
        }

        return strlen( $s )
            ? rtrim( $s, ', ' )
            : null;
    }
}
