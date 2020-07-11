<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use App\Http\Action;
use Slim\App;

return static function (App $app): void {
    $uuidRegexp = '[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}';
    $app->post(
        '/pages',
        Action\Page\CreateAction::class
    );
    $app->get(
        '/pages', // useDisplayValues
        Action\Page\ListAction::class
    );
    $app->get(
        '/pages/{id:' . $uuidRegexp . '}', // depth, parentId, format
        Action\Page\DetailAction::class
    );
    $app->put(
        '/pages/{id:' . $uuidRegexp . '}',
        Action\Page\UpdateAction::class
    );
    $app->patch(
        '/pages/{id:' . $uuidRegexp . '}',
        Action\Page\PatchAction::class
    );
    $app->delete(
        '/pages/{id:' . $uuidRegexp . '}',
        Action\Page\DeleteAction::class
    );
    $app->post(
        '/uploads',
        Action\Upload\UploadAction::class
    );
    $app->post(
        '/uploads/{id:' . $uuidRegexp . '}/formats/{extension:[-_0-9a-zA-Z]+}',
        Action\Upload\CreateFormatAction::class
    );
    $app->post(
        '/uploads/{id:' . $uuidRegexp . '}/presets/{preset:[-_0-9a-zA-Z]+}',
        Action\Upload\CreatePresetAction::class
    );
    $app->post(
        '/uploads/{id:' . $uuidRegexp . '}/presets/{preset:[-_0-9a-zA-Z]+}/formats/{extension:[0-9a-z]+}',
        Action\Upload\CreatePresetFormatAction::class
    );
};
