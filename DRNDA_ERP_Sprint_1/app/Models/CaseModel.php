<?php
namespace App\Models;

final class CaseModel
{
    public static function all(?string $query = null): array
    {
        $rows = read_data('cases');
        if ($query) {
            $q = mb_strtolower($query);
            $rows = array_values(array_filter($rows, fn(array $r): bool => str_contains(mb_strtolower($r['case_number'].' '.$r['deceased_name'].' '.$r['contact_name']), $q)));
        }
        usort($rows, fn(array $a, array $b): int => strcmp($a['funeral_at'], $b['funeral_at']));
        return $rows;
    }
    public static function find(int $id): ?array
    {
        foreach (read_data('cases') as $row) if ((int)$row['id'] === $id) return $row;
        return null;
    }
    public static function create(array $data): int
    {
        $rows = read_data('cases');
        $id = $rows ? max(array_column($rows, 'id')) + 1 : 1;
        $rows[] = ['id' => $id] + $data + ['created_at' => date(DATE_ATOM)];
        write_data('cases', $rows);
        return $id;
    }
    public static function nextNumber(): string
    {
        $rows = read_data('cases');
        $next = $rows ? max(array_column($rows, 'id')) + 1 : 1;
        return sprintf('PO-%s-%06d', date('Y'), $next);
    }
}
