<?php namespace App\Console\Commands;

use App\Nrgi\Repositories\Contract\ContractRepository;
use Aws\CloudFront\Exception\Exception;
use Illuminate\Console\Command;

class HarmonizeCompanyNames extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nrgi:harmonizecompanynames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harmonize Company Names.';
    /**
     * @var ContractRepository
     */
    protected $contract;

    /**
     * Create a new command instance.
     *
     * @param ContractRepository $contractRepository
     *
     * @internal param ContractRepository $contract
     */
    public function __construct(ContractRepository $contractRepository)
    {
        parent::__construct();
        $this->contract = $contractRepository;
    }

    /**
     * Execute the console command.
     *
     */

    public function fire()
    {
        $oldCompanyNames = $this->contract->getAllCompanyNames();

        $newCompanyNames = $this->harmonizeCompanyNames($oldCompanyNames);
        $this->updateCompanyName($newCompanyNames);
    }

    /**
     * Harmonize company name for contract
     *
     * @param $oldCompanyNames
     *
     * @return mixed
     */
    public function harmonizeCompanyNames($oldCompanyNames)
    {
        foreach ($oldCompanyNames as $oldname) {
            $old = $oldname['company_name'];
            $new = isset(trans('codelist/company_name')[$old]) ? trans('codelist/company_name')[$old] : '';
            if (empty($new)) {
                $oldname['company_name'] = $old;
            }
            $oldname['company_name'] = $new;
        }

        return ($oldCompanyNames);
    }

    /**
     * Update Company Name
     *
     * @param $companyName
     */
    public function updateCompanyName($companyName)
    {
        foreach ($companyName as $company) {
            try {
                $contract = $this->contract->findContract($company['id']);
                $metadata = json_decode(json_encode($contract->metadata->company), true);
                foreach ($metadata as $m) {
                    $m['name'] = $company['company_name'];
                }
                $contract->metadata = $metadata;
                $contract->save();
                $this->info(sprintf('Company Name %s : UPDATED', $contract->id));
            } catch (Exception $e) {
                $this->logger->error('update Company Name : '.$e->getMessage());
            }
        }
    }
}
