<?php namespace App\Console\Commands;

use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Filesystem\Filesystem;
use ZipArchive;

/*
 * Bulk download of pdf text
 */

class BulkdownloadText extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nrgi:bulktext';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download All pdf Text in zip file.';
    /**
     * @var
     */
    public $storage;
    /**
     * @var
     */
    public $filesystem;

    const RAWTEXT = "rawtext";
    const REFINEDTEXT = "refinedtext";
    const S3FOLDER = "dumptext";
    const S3RCFOLDER = "rcdumptext";
    const S3OLCFOLDER = "olcdumptext";

    /**
     * @param Storage    $storage
     * @param Filesystem $filesystem
     */
    public function __construct(Storage $storage, Filesystem $filesystem)
    {
        parent::__construct();
        $this->storage    = $storage;
        $this->filesystem = $filesystem;
    }


    /**
     * Execute bash file for all contracts , rc and olc
     */
    public function fire()
    {
        $host        = env('DB_HOST');
        $port        = env('DB_PORT');
        $user        = env('DB_USERNAME');
        $database    = env('DB_DATABASE');
        $storagepath = storage_path();
        $rawText     = self::RAWTEXT;
        $refinedText = self::REFINEDTEXT;
        $password    = str_replace("&", "\&", env('DB_PASSWORD'));
        $path        = __DIR__.'/BashScript';
        $date        = date('Y_m_d');
        $filename    = $date;
        $country     = [
            'TN' => 'TN',
            'GN' => 'GN',
            'SL' => 'SL',
        ];


        $this->extractAllText(
            $host,
            $port,
            $user,
            $database,
            $password,
            $storagepath,
            $filename,
            $rawText,
            $refinedText,
            $path
        );
        $this->extractCategoryText(
            $host,
            $port,
            $user,
            $database,
            $password,
            $storagepath,
            $filename,
            $rawText,
            $refinedText,
            $path,
            "rc"
        );
        $this->extractCategoryText(
            $host,
            $port,
            $user,
            $database,
            $password,
            $storagepath,
            $filename,
            $rawText,
            $refinedText,
            $path,
            "olc"
        );

        foreach ($country as $key => $value) {
            $this->extractCategoryCountryText(
                $host,
                $port,
                $user,
                $database,
                $password,
                $storagepath,
                $filename,
                $rawText,
                $refinedText,
                $path,
                $value
            );
        }
        $this->uploadZipFile($storagepath);
        $this->deleteFromLocal($path, $storagepath);

    }

    /**
     * Extract text file of all contracts
     *
     * @param $host
     * @param $port
     * @param $user
     * @param $database
     * @param $password
     * @param $storagepath
     * @param $filename
     * @param $rawText
     * @param $refinedText
     * @param $path
     */
    public function extractAllText(
        $host,
        $port,
        $user,
        $database,
        $password,
        $storagepath,
        $filename,
        $rawText,
        $refinedText,
        $path
    ) {
        $alltext = "alltext";

        chdir($path);
        chmod($path.'/extract.sh', 0777);
        echo shell_exec(
            "./extract.sh $host $port $user $database $storagepath $password $alltext $filename $rawText $refinedText"
        );
        $this->info("File zipped");
        //$this->uploadZipFile($storagepath, $filename, $alltext);


    }

    /**
     * Extract text file of rc and olc
     *
     * @param $host
     * @param $port
     * @param $user
     * @param $database
     * @param $password
     * @param $storagepath
     * @param $filename
     * @param $rawText
     * @param $refinedText
     * @param $path
     * @param $category
     */
    public function extractCategoryText(
        $host,
        $port,
        $user,
        $database,
        $password,
        $storagepath,
        $filename,
        $rawText,
        $refinedText,
        $path,
        $category
    ) {

        $categorytext = "categorytext";
        chdir($path);
        chmod($path.'/extractcategory.sh', 0777);
        echo shell_exec(
            "./extractcategory.sh $host $port $user $database $storagepath $password $categorytext $filename $rawText $refinedText $category"
        );
        $this->info("File zipped");
        //$this->uploadZipFile($storagepath, $filename, $categorytext, $category);

    }

    /**
     * Extract text file of country
     *
     * @param $host
     * @param $port
     * @param $user
     * @param $database
     * @param $password
     * @param $storagepath
     * @param $filename
     * @param $rawText
     * @param $refinedText
     * @param $path
     * @param $category
     */
    public function extractCategoryCountryText(
        $host,
        $port,
        $user,
        $database,
        $password,
        $storagepath,
        $filename,
        $rawText,
        $refinedText,
        $path,
        $category
    ) {

        $categorytext = "categorytext";
        chdir($path);
        chmod($path.'/extractcategory-country.sh', 0777);
        echo shell_exec(
            "./extractcategory-country.sh $host $port $user $database $storagepath $password $categorytext $filename $rawText $refinedText $category"
        );
        $this->info("File zipped");
        //$this->uploadZipFile($storagepath, $filename, $categorytext, $category);

    }


    /**
     * Upload file in s3
     *
     * @param $filename
     */
    public function uploadZipFile($storagepath)
    {
        $s3folder = self::S3FOLDER;

        $client = S3Client::factory(
            [
                'key'    => env('AWS_KEY'),
                'secret' => env('AWS_SECRET'),
                'region' => env('AWS_REGION'),
            ]
        );

        $client->uploadDirectory($storagepath."/download/", env('AWS_BUCKET'), "/".$s3folder);
        $this->info("File uploaded in s3");
    }

    public function deleteFromLocal($path, $storagepath)
    {
        chdir($path);
        chmod($path.'/deleteFromLocal.sh', 0777);
        echo shell_exec(
            "./deleteFromLocal.sh $storagepath"
        );
        $this->info("File(s) Deleted");
    }


}
