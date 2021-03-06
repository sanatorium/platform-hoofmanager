<?php namespace Sanatorium\Hoofmanager\Controllers\Api;

use Sanatorium\Hoofmanager\Repositories\Apilog\ApilogRepositoryInterface;
use Sanatorium\Hoofmanager\Repositories\Houses\HousesRepositoryInterface;

use Input;

class HousesController extends ApiController {

    /**
     * {@inheritDoc}
     */
    protected $csrfWhitelist = [
        'executeAction',
    ];

    /**
     * The Hoofmanager repository.
     *
     * @var \Sanatorium\Hoofmanager\Repositories\Houses\HousesRepositoryInterface
     */
    protected $houses;

    /**
     * Holds all the mass actions we can execute.
     *
     * @var array
     */
    protected $actions = [
        'delete',
        'enable',
        'disable',
    ];

    /**
     * Constructor.
     *
     * @param  \Sanatorium\Hoofmanager\Repositories\Houses\HousesRepositoryInterface  $houses
     * @param  \Sanatorium\Hoofmanager\Repositories\Apilog\ApilogRepositoryInterface $apilogs
     * @return void
     */
    public function __construct(ApilogRepositoryInterface $apilogs,
                                HousesRepositoryInterface $houses)
    {
        parent::__construct($apilogs);

        $this->houses = $houses;

        $this->apilogs = $apilogs;
    }

    /**
     * Display a listing of houses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('sanatorium/hoofmanager::houses.index');
    }

    /**
     * Datasource for the houses Data Grid.
     *
     * @return \Cartalyst\DataGrid\DataGrid
     */
    public function grid()
    {
        $data = $this->houses->grid();

        $columns = [
            'id',
            'cattle_number',
            'created_at',
        ];

        $settings = [
            'sort'      => 'created_at',
            'direction' => 'desc',
        ];

        $transformer = function($element)
        {
            $element->edit_uri = route('admin.sanatorium.hoofmanager.houses.edit', $element->id);

            return $element;
        };

        return datagrid($data, $columns, $settings, $transformer);
    }

    public function simple()
    {
        $cols = Input::has('cols') ? Input::get('cols') : ['id', 'cattle_number'];

        $data = $this->houses->all();

        $result = [];

        foreach( $data as $item ) {
            $result_item = [];

            foreach( $cols as $col ) {
                $result_item[$col] = $item->{$col};
            }

            $result[] = $result_item;
        }

        return $result;
    }

    /**
     * Show the form for creating new houses.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->showForm('create');
    }

    /**
     * Handle posting of the form for creating new houses.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        return $this->processForm('create');
    }

    /**
     * Show the form for updating houses.
     *
     * @param  int  $id
     * @return mixed
     */
    public function edit($id)
    {
        return $this->showForm('update', $id);
    }

    /**
     * Handle posting of the form for updating houses.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        return $this->processForm('update', $id);
    }

    /**
     * Remove the specified houses.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $type = $this->houses->delete($id) ? 'success' : 'error';

        $this->alerts->{$type}(
            trans("sanatorium/hoofmanager::houses/message.{$type}.delete")
        );

        return redirect()->route('admin.sanatorium.hoofmanager.houses.all');
    }

    /**
     * Executes the mass action.
     *
     * @return \Illuminate\Http\Response
     */
    public function executeAction()
    {
        $action = request()->input('action');

        if (in_array($action, $this->actions))
        {
            foreach (request()->input('rows', []) as $row)
            {
                $this->houses->{$action}($row);
            }

            return response('Success');
        }

        return response('Failed', 500);
    }

    /**
     * Shows the form.
     *
     * @param  string  $mode
     * @param  int  $id
     * @return mixed
     */
    protected function showForm($mode, $id = null)
    {
        // Do we have a houses identifier?
        if (isset($id))
        {
            if ( ! $houses = $this->houses->find($id))
            {
                $this->alerts->error(trans('sanatorium/hoofmanager::houses/message.not_found', compact('id')));

                return redirect()->route('admin.sanatorium.hoofmanager.houses.all');
            }
        }
        else
        {
            $houses = $this->houses->createModel();
        }

        // Show the page
        return view('sanatorium/hoofmanager::houses.form', compact('mode', 'houses'));
    }

    /**
     * Processes the form.
     *
     * @param  string  $mode
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function processForm($mode, $id = null)
    {
        $data = request()->all();

        if ( isset($data['items']) ) {

            foreach ( $data['items'] as &$item ) {

                unset($item['findings']);

            }

        }

        // Store the houses
        list($messages, $house) = $this->houses->store($id, $data);

        // Do we have any errors?
        if ($messages->isEmpty())
        {
            return $house;
        }

        return ['success' => false];
    }

}
