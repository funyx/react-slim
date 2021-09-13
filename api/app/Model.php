<?php

namespace App;

use PDO;
use Pimple\Container;
use Respect\Validation\Rules\AllOf;

class Model extends Container {
    protected PDO $pdo;

    protected array $initial_state = [
        'data' => []
    ];

    protected array $public = [];

    protected string $table = '';

    protected array $fillable_fields = [];

    protected array $fields = [];

    protected bool $use_dates = true;

    protected array $public_fields;
    /**
     * @var \App\QueryBuilder
     */
    private QueryBuilder $qb;
    private string $query;
    protected string $intent = 'select';

    public function __construct(PDO $pdo = null, $values = []) {
        parent::__construct( array_merge( $this->initial_state, $values ) );
        if( is_null( $pdo ) ) {
            global $c;
            /* @var \Pdo $pdo */
            $pdo = $c['pdo'];
        }
        $this->pdo = $pdo;
        $this->fields = $this->normalizeFields( array_merge( $this->fields(), $this->addDates(), [
            'id' => [
                'type'   => PDO::PARAM_INT,
                'system' => true
            ]
        ] ) );
        $this->qb = new QueryBuilder( [
            'pdo'    => fn() => $this->pdo,
            'table'  => $this->table,
            'fields' => $this->fields,
        ] );
    }

    private function normalizeFields(array $fields): array {
        foreach( $fields as $field_name => $field_settings ) {
            if( is_int( $field_name ) && is_string( $field_settings ) ) {
                $fields[$field_settings] = [ 'type' => PDO::PARAM_STR ];
                unset( $fields[$field_name] );
            }
        }

        return $fields;
    }

    protected function fields(array $fields = []): array {
        return $fields;
    }

    private function addDates(): array {
        if( $this->use_dates ) {
            return [
                'created_at' => [
                    'type'     => PDO::PARAM_STR,
                    'default'  => fn() => ( new \DateTime() )->format( config( 'format.datetime' ) ),
                    'system'   => true,
                    'nullable' => false
                ],
                'updated_at' => [
                    'type'     => PDO::PARAM_STR,
                    'system'   => true,
                    'nullable' => true
                ]
            ];
        }

        return [];
    }

    public function orderBy(array $order): static {
        $this->qb->orderBy( $order );

        return $this;
    }

    public function limit(int $limit = 10, int $offset = 0): static {
        $this->qb->limit( $limit, $offset );

        return $this;
    }

    public function loadAny(): static {
        $this->offsetSet( 'data', [] );

        return $this->loadResult();
    }

    private function loadResult(bool $many = false, array $fields = []) {
        $this->query = $this->qb->selectStatement( fields: $fields );
        $q = $this->pdo->prepare( $this->query );
        if( $q->execute() ) {
            $many
                ? $data = $q->fetchAll()
                : $data = $q->fetch();
            if( $data ) {
                $this->offsetSet( 'data', $data );
            }
        }

        return $this;
    }

    public function loadBy($field, $value): static {
        $this->offsetSet( 'data', [] );
        $this->addCondition( $field, '=', $value );

        return $this->loadResult();
    }

    public function addCondition(string $field, string $operator, string|null $value): self {
        $this->qb->addCondition( $field, $operator, $value );

        return $this;
    }

    public function export(array $fields = []): static {
        if( !empty( $fields ) ) {
            $tmp = $fields;
            $fields = [];
            foreach( $tmp as $field_name ) {
                if( array_key_exists( $field_name, $this->fields ) ) {
                    $fields[$field_name] = $this->fields[$field_name];
                }
            }
            unset( $tmp );
        }

        return $this->loadResult( many: true, fields: $fields );
    }

    public function uuid_v4(): string {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public function getQuery() {
        return $this->query;
    }

    public function validate(array $data, string $intent = 'select'): array {
        $this->intent = $intent;
        $validation_errors = [];
        foreach( $this->fields as $field_name => $settings ) {
            if(
                is_null( $data[$field_name] ?? null )
                && !( $settings['nullable'] ?? ( $settings['required'] ?? false ) )
                && ( $settings['default'] ?? false )
            ) {
                $data[$field_name] = is_callable( $settings['default'] )
                    ? ( $settings['default'] )()
                    : $settings['default'] ?? null;
            }
            if( array_key_exists( $field_name, $data ) ) {
                if( array_key_exists( 'validation', $settings ) ) {
                    if( $settings['validation'] instanceof AllOf ) {
                        try {
                            $settings['validation']->setName( $field_name )->assert( $data[$field_name] );
                        } catch( \Respect\Validation\Exceptions\NestedValidationException $e ) {
                            $validation_errors[$field_name] = array_values( $e->getMessages() );
                        }
                    }
                }
            }
        }
        $this->intent = 'select';
        $this->set( $data );

        return $validation_errors;
    }

    public function set(array $data): static {
        foreach($data as $key => $value){
            if(!$this->loaded() && array_key_exists('cast', $this->fields[$key]) && is_callable($this->fields[$key]['cast'])){
                $data[$key] = $this->fields[$key]['cast']($value);
            }
        }
        $this->offsetSet( 'data', $data );

        return $this;
    }

    public function insert(): static {
        if( $data = $this->offsetGet( 'data' ) ) {
            $que = [];
            $this->query = $this->qb->insertStatement(
                data: $data,
                fields: $this->fields
            );

            $stmt = $this->pdo->prepare( $this->query );
            foreach( $data as $field => $value ) {
                $f = $this->fields[$field];
                if( $f['type'] === 'Expression' ) {
                    if( array_key_exists( 'insert', $f ) ) {
                        $e = $this->pdo->prepare( $f['insert'] );
                        $e->bindValue( ':' . $field, $value, PDO::PARAM_STR );
                        if( array_key_exists( 'insert_bind', $f ) ) {
                            foreach( $f['insert_bind'] as $param ) {
                                $f_param = $this->fields[$param];
                                $e->bindValue( ':' . $param, $data[$param], $f_param['type'] );
                            }
                        }
                        $que[] = $e;
                    }
                    continue;
                }
                $stmt->bindValue( ':' . $field, $value, $f['type'] );
            }
            $this->pdo->beginTransaction();
            try {
                $stmt->execute();
                $id = $this->pdo->lastInsertId();
                while( !empty( $que ) ) {
                    /* @var \PDOStatement $e */
                    $e = array_shift( $que );
                    $e->execute();
                }
                $this->pdo->commit();

                return $this->load( $id );
            } catch( \PDOException $e ) {
                if( $this->pdo->inTransaction() ) {
                    $this->pdo->rollBack();
                }
                throw $e;
            }
        }

        return $this;
    }

    public function load(int|array|null $arr): self {
        $this->offsetSet( 'data', [] );
        $this->qb->reset();
        if( is_int( $arr ) ) {
            $this->addCondition( 'id', '=', $arr );
        } elseif( is_array( $arr ) ) {
            $this->addCondition( ...$arr );
        }

        $this->loadResult();

        return $this;
    }

    public function update(array $array): static {
        if( count( $array ) ) {
            if( $this->loaded() ) {
                $this->resetConditions();
                $this->addCondition( 'id', '=', $this->get()['id'] );
            }
            if( array_key_exists( 'updated_at', $this->fields ) ) {
                $data['updated_at'] = ( new \DateTime() )->format( config( 'format.datetime' ) );
            }
            $data = array_filter( array_merge( $data, $array ), function($v, $k) {
                // ignore fields, could be freezed props if a field container is present
                return !in_array( $k, [ 'id', 'uuid', 'created_at' ] );
            }, ARRAY_FILTER_USE_BOTH );
            $this->offsetSet( 'data', $data );
            $que = [];
            $this->query = $this->qb->updateStatement(
                data: $data,
                fields: $this->fields
            );
            $stmt = $this->pdo->prepare( $this->query );
            foreach( $data as $field => $value ) {
                $f = $this->fields[$field];
                if( $f['type'] === 'Expression' ) {
                    if( array_key_exists( 'update', $f ) ) {
                        $e = $this->pdo->prepare( $f['update'] );
                        $e->bindValue( ':' . $field, $value, PDO::PARAM_STR );
                        if( array_key_exists( 'update_bind', $f ) ) {
                            foreach( $f['update_bind'] as $param ) {
                                $f_param = $this->fields[$param];
                                $e->bindValue( ':' . $param, $data[$param], $f_param['type'] );
                            }
                        }
                        $que[] = $e;
                    }
                    continue;
                }
                $stmt->bindValue( ':' . $field, $value, $f['type'] );
            }
            $this->pdo->beginTransaction();
            try {
                $stmt->execute();
                while( !empty( $que ) ) {
                    /* @var \PDOStatement $e */
                    $e = array_shift( $que );
                    $e->execute();
                }
                $this->pdo->commit();
            } catch( \PDOException $e ) {
                if( $this->pdo->inTransaction() ) {
                    $this->pdo->rollBack();
                }
                throw $e;
            }
        }

        return $this;
    }

    public function loaded(): bool {
        return !empty( $this->offsetGet( 'data' ) );
    }

    public function resetConditions(): self {
        $this->qb->offsetSet( 'conditions', [] );

        return $this;
    }

    public function get() {
        return $this->raw( 'data' );
    }

    public function publicFields() {
        return array_filter( $this->get(), function($v, $k) {
            return in_array( $k, ( $this->public_fields ?? array_keys( $this->fields ) ) );
        }, ARRAY_FILTER_USE_BOTH );
    }

    public function getPublicFields(array $add = [] ): array {
        return array_merge($this->public_fields, $add);
    }
}
