<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\CreateBladeMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\EditBladeMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\IndexBladeMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\ShowBladeMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\ControllerMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\ControllerTestMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\StoreRequestMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\UpdateRequestMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\SeederMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\ModelMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\MigrationMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\FactoryMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\FileMaker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Maker\JsonMaker;

class MakeCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {modelName?} {--schema= : {pathToJson}} {--table= : {tableName}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CRUDテンプレートを自動生成します。';

    /**
     * Default JSON path for database schema.
     *
     * @var string
     */
    protected $databaseSchemaJson = __DIR__ . '/../../../storage/myfiles/default_schema.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 引数バリデーション ------------------------------------------------
        if(!$this->argument('modelName')) {
            $this->error("modelNameを入力して下さい。");
            return Command::INVALID;
        }

        if($this->option('schema') && $this->option('table')) {
            $this->error("tableオプションとschemaオプションは同時に指定できません。");
            return Command::INVALID;
        }

        if($this->option('schema') && !file_exists($this->option('schema'))) {
            $this->error("指定されたJSONファイルが存在しません。");
            return Command::INVALID;
        }

        if($this->option('schema') && pathinfo($this->option('schema'), PATHINFO_EXTENSION) !== 'json') {
            $this->error("schemaにはJSONファイルを指定してください。");
            return Command::INVALID;
        } 

        // テーブルスキーマを定義 ------------------------------------------------
        // - JSON定義を文字列として取得
        $jsonStr = '';
        if($this->option('table')) {
            $path = __DIR__ . "/../../../storage/myfiles/" . date('Ymd_His') . "_" . Str::snake(Str::plural($this->argument('modelName')), '_') . ".json";
            $json = $this->generateTableSchemaJson($this->option('table'), $this->argument('modelName'), $path);
            $jsonStr = file_get_contents($json);
        } else if($this->option('schema')) {
            $jsonStr = str_replace("%%TABLE_NAME%%", $this->argument('modelName'), file_get_contents($this->option('schema')));
        } else {
            $jsonStr = str_replace("%%TABLE_NAME%%", $this->argument('modelName'), file_get_contents($this->databaseSchemaJson));
        }
        // - schema作成
        $schema = new DatabaseSchema($jsonStr);

        // テーブルスキーマのバリデーション ------------------------------------------------
        if($schema->isDuplicateTimeStamp()) {
            $this->error("timestamp=trueとcreated_at,updated_atカラムを同時に指定することはできません。 \n timestampをfalseにするか、created_at,updated_atカラムの名前を変更してください。");
            return Command::INVALID;
        }

        // 生成したschemaを元にCRUDテンプレートを生成 -------------------------
        $this->makeCrud($schema, $this->argument('modelName'));
        $this->info(Str::singular($this->argument('modelName')) . " created successfully");

        return Command::SUCCESS;
    }

    protected function generateTableSchemaJson(string $table, string $modelName, string $jsonFilePath): string
    {
        $schemaManager = DBConnect::getSchmaManager();
        $columns = $schemaManager->listTableColumns($table);
        $jsonData = JsonMaker::getJsonFor($modelName, false, false, $columns);
        $this->storage($jsonData, $jsonFilePath);
        return $jsonFilePath;
    }

    protected function makeCrud(DatabaseSchema $schema, string $modelName) : void {
        FileMaker::mkdirIfNotExists($modelName); // FIXME: ここでやるべきじゃない。個別のMakerでやるべき
        (new CreateBladeMaker)->generate($schema, $modelName);
        (new EditBladeMaker)->generate($schema, $modelName);
        (new IndexBladeMaker)->generate($schema, $modelName);
        // ShowBladeMaker::getShowBladeBy($schema, $modelName);
        (new ControllerMaker)->generate($schema, $modelName);
        (new ControllerTestMaker)->generate($schema, $modelName);
        (new StoreRequestMaker)->generate($schema, $modelName);
        (new UpdateRequestMaker)->generate($schema, $modelName);
        (new ModelMaker)->generate($schema, $modelName);
        (new MigrationMaker)->generate($schema, $modelName);
        (new FactoryMaker)->generate($schema, $modelName);
        (new SeederMaker)->generate($modelName);
        self::updateDatabaseSeeder($modelName);
        self::updateWebphp($modelName);
    }

    private function storage(string $json, string $filePath) : void {
        file_put_contents($filePath, $json);
    }

    private static function updateWebphp(string $modelName) : void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        $plural = Str::plural($singular);
        $chainPlural = Str::snake($plural, '-');
        $file = './routes/web.php';
        $current = file_get_contents($file);
        $current .= "\nRoute::resource('{$chainPlural}'". ",'App\Http\Controllers\\{$upperSingular}Controller');";
        file_put_contents($file, $current);
    }

    private static function updateDatabaseSeeder($modelName): void {
        $singular = Str::singular($modelName);
        $upperSingular = Str::ucfirst($singular);
        //public function run() {を置換する
        //(改行の仕方が違うかもしれないので、よく見る3パターンを置換する)
        $arrayReplace = array(
            "public function run()\n    {" => "public function run()\n    {\n\t\t\$this->call({$upperSingular}Seeder::class);",
            "public function run(): void\n    {" => "public function run()\n    {\n\t\t\$this->call({$upperSingular}Seeder::class);",
            "public function run() {" => "public function run()\n    {\n\t\t\$this->call({$upperSingular}Seeder::class);",
            "public function run(){" => "public function run()\n    {\n\t\t\$this->call({$upperSingular}Seeder::class);",
        );
        $fileName = './database/seeders/Databaseseeder.php';
        $buff = file_get_contents($fileName);
        $buff = strtr($buff, $arrayReplace);
        file_put_contents($fileName, $buff);
    }
}
