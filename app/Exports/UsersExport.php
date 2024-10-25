<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UsersExport implements FromCollection
{
    public function collection()
    {
        $tables = DB::table('information_schema.tables')
            ->whereIn('table_type', ['BASE TABLE', 'VIEW'])
            ->whereNotIn('TABLE_NAME', [
                'password_reset_tokens',
                'oauth_auth_codes',
                'oauth_access_tokens',
                'oauth_refresh_tokens',
                'oauth_clients',
                'oauth_personal_access_clients',
                'failed_jobs',
                'personal_access_tokens',
                'migrations'
            ])
            ->where('TABLE_SCHEMA', 'donations')
            ->get('TABLE_NAME');

        $allData = [];

        foreach ($tables as $table) {

            $allData[] = ['table_name' => $table->TABLE_NAME];
            $cols = Schema::getColumnListing($table->TABLE_NAME);
            $allData[] = $cols;
            $records = DB::table($table->TABLE_NAME)->select('*')->get();
            foreach ($records as $r) {
                $allData[] = $r;
            }
        }

        return collect($allData);
    }
}
