<?php

// Generador único de fixture patrimonio SIGA (ejecutar: php database/fixtures/integrations/siga/generate_patrimonio.php)

$units = [
    ['ORG-061', 'SIGA-U-061'],
    ['ORG-052', 'SIGA-U-052'],
    ['ORG-059', 'SIGA-U-059'],
    ['ORG-048', 'SIGA-U-048'],
    ['ORG-057', 'SIGA-U-057'],
    ['ORG-058', 'SIGA-U-058'],
];
$tipos = ['pc', 'pc', 'pc', 'impresora', 'servidor', 'red', 'otro'];
$marcas = ['HP', 'Dell', 'Lenovo', 'Canon', 'Cisco'];
$items = [];

for ($i = 1; $i <= 25; $i++) {
    $u = $units[$i % count($units)];
    $items[] = [
        'codigo_siga' => sprintf('SIGA-EQ-%03d', $i),
        'codigo_patrimonial' => sprintf('PAT-2024-%03d', $i),
        'categoria' => $i <= 22 ? 'informatica_municipal' : 'mobiliario',
        'tipo_equipo' => $tipos[$i % count($tipos)],
        'marca' => $marcas[$i % count($marcas)],
        'modelo' => 'Modelo-'.$i,
        'numero_serie' => 'SN'.str_pad((string) $i, 8, '0', STR_PAD_LEFT),
        'estado_operativo' => $i % 9 === 0 ? 'reparacion' : 'operativo',
        'codigo_org' => $u[0],
        'unidad_codigo_siga' => $u[1],
        'custodio_nombre' => 'Custodio Demo '.$i,
        'custodio_cargo' => 'Especialista',
        'valor_patrimonial' => 1500 + ($i * 120),
        'fecha_adquisicion' => sprintf('2022-%02d-15', ($i % 12) + 1),
    ];
}

$path = __DIR__.'/patrimonio.json';
file_put_contents($path, json_encode(['items' => $items], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Generado: {$path}\n";
