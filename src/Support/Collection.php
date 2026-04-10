<?php

namespace LunoxHoshizaki\Support;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use Traversable;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The items contained in the collection.
     */
    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Create a new collection instance.
     */
    public static function make(array $items = []): static
    {
        return new static($items);
    }

    /**
     * Get all items in the collection.
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get the first item.
     */
    public function first(?callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            return empty($this->items) ? $default : reset($this->items);
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get the last item.
     */
    public function last(?callable $callback = null, mixed $default = null): mixed
    {
        if (is_null($callback)) {
            return empty($this->items) ? $default : end($this->items);
        }

        return static::make(array_reverse($this->items, true))->first($callback, $default);
    }

    /**
     * Run a map over each of the items.
     */
    public function map(callable $callback): static
    {
        return new static(array_map($callback, $this->items, array_keys($this->items)));
    }

    /**
     * Run a filter over each of the items.
     */
    public function filter(?callable $callback = null): static
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }

        return new static(array_filter($this->items));
    }

    /**
     * Filter items by the given key-value pair.
     */
    public function where(string $key, mixed $value): static
    {
        return $this->filter(function ($item) use ($key, $value) {
            $itemValue = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            return $itemValue === $value;
        });
    }

    /**
     * Get the values of a given key.
     */
    public function pluck(string $key): static
    {
        $results = [];
        foreach ($this->items as $item) {
            $results[] = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
        }
        return new static($results);
    }

    /**
     * Reduce the collection to a single value.
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Run a callback on each item.
     */
    public function each(callable $callback): static
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
        return $this;
    }

    /**
     * Sort the collection.
     */
    public function sortBy(string|callable $key, bool $descending = false): static
    {
        $items = $this->items;
        
        usort($items, function ($a, $b) use ($key, $descending) {
            if (is_callable($key)) {
                $valueA = $key($a);
                $valueB = $key($b);
            } else {
                $valueA = is_array($a) ? ($a[$key] ?? null) : ($a->$key ?? null);
                $valueB = is_array($b) ? ($b[$key] ?? null) : ($b->$key ?? null);
            }
            
            $result = $valueA <=> $valueB;
            return $descending ? -$result : $result;
        });

        return new static($items);
    }

    /**
     * Reverse the collection order.
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->items));
    }

    /**
     * Get a slice of the collection.
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return new static(array_slice($this->items, $offset, $length, true));
    }

    /**
     * Take the first or last {$limit} items.
     */
    public function take(int $limit): static
    {
        if ($limit < 0) {
            return $this->slice($limit, abs($limit));
        }
        return $this->slice(0, $limit);
    }

    /**
     * Chunk the collection into chunks of the given size.
     */
    public function chunk(int $size): static
    {
        $chunks = [];
        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }
        return new static($chunks);
    }

    /**
     * Get unique items from the collection.
     */
    public function unique(?string $key = null): static
    {
        if (is_null($key)) {
            return new static(array_unique($this->items, SORT_REGULAR));
        }

        $seen = [];
        return $this->filter(function ($item) use ($key, &$seen) {
            $value = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            if (in_array($value, $seen, true)) {
                return false;
            }
            $seen[] = $value;
            return true;
        });
    }

    /**
     * Flatten a multi-dimensional collection.
     */
    public function flatten(int $depth = PHP_INT_MAX): static
    {
        $results = [];

        foreach ($this->items as $item) {
            if (is_array($item) && $depth > 0) {
                $results = array_merge($results, static::make($item)->flatten($depth - 1)->all());
            } else {
                $results[] = $item;
            }
        }

        return new static($results);
    }

    /**
     * Merge the collection with the given items.
     */
    public function merge(array $items): static
    {
        return new static(array_merge($this->items, $items));
    }

    /**
     * Determine if an item exists in the collection.
     */
    public function contains(mixed $key, mixed $value = null): bool
    {
        if (is_callable($key)) {
            return $this->first($key) !== null;
        }

        if (!is_null($value)) {
            return $this->where($key, $value)->isNotEmpty();
        }

        return in_array($key, $this->items, true);
    }

    /**
     * Get the sum of a given key.
     */
    public function sum(string|callable|null $key = null): int|float
    {
        if (is_null($key)) {
            return array_sum($this->items);
        }

        return $this->pluck(is_string($key) ? $key : '')->reduce(function ($carry, $item) use ($key) {
            return $carry + (is_callable($key) ? $key($item) : $item);
        }, 0);
    }

    /**
     * Get the average of a given key.
     */
    public function avg(string|null $key = null): int|float|null
    {
        $count = $this->count();
        if ($count === 0) {
            return null;
        }
        return $this->sum($key) / $count;
    }

    /**
     * Get the min value of a given key.
     */
    public function min(string|null $key = null): mixed
    {
        $values = $key ? $this->pluck($key)->all() : $this->items;
        return min($values);
    }

    /**
     * Get the max value of a given key.
     */
    public function max(string|null $key = null): mixed
    {
        $values = $key ? $this->pluck($key)->all() : $this->items;
        return max($values);
    }

    /**
     * Key the collection by a given field.
     */
    public function keyBy(string $key): static
    {
        $results = [];
        foreach ($this->items as $item) {
            $keyValue = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            $results[$keyValue] = $item;
        }
        return new static($results);
    }

    /**
     * Group items by a given key.
     */
    public function groupBy(string $key): static
    {
        $results = [];
        foreach ($this->items as $item) {
            $groupKey = is_array($item) ? ($item[$key] ?? '') : ($item->$key ?? '');
            $results[$groupKey][] = $item;
        }
        return new static($results);
    }

    /**
     * Convert the collection to an array.
     */
    public function toArray(): array
    {
        return array_map(function ($item) {
            if (is_object($item) && method_exists($item, 'toArray')) {
                return $item->toArray();
            }
            return $item;
        }, $this->items);
    }

    /**
     * Convert the collection to JSON.
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Determine if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Determine if the collection is not empty.
     */
    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    // --- Interface implementations ---

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}
