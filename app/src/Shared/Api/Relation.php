<?php

declare(strict_types=1);

namespace App\Shared\Api;

class Relation
{
    /**
     * @param array<int|string>|int|string $set
     * @param array<int|string>|int|string $add
     * @param array<int|string>|int|string $remove
     */
    public function __construct(
        public array|int|string $set = [] {
            get {
                if (is_array($this->set)) {
                    return $this->set;
                }

                return $this->set === '' ? [] : [$this->set];
            }
        },
        public array|int|string $add = [] {
            get {
                if (is_array($this->add)) {
                    return $this->add;
                }

                return $this->add === '' ? [] : [$this->add];
            }
        },
        public array|int|string $remove = [] {
            get {
                if (is_array($this->remove)) {
                    return $this->remove;
                }

                return $this->remove === '' ? [] : [$this->remove];
            }
        }
    )
    {

    }
}
