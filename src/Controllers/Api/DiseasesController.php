<?php namespace Sanatorium\Hoofmanager\Controllers\Api;

use Sanatorium\Hoofmanager\Repositories\Apilog\ApilogRepositoryInterface;
use Sanatorium\Hoofmanager\Repositories\Diseases\DiseasesRepositoryInterface;

use Input;

class DiseasesController extends ApiController {

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * The Hoofmanager repository.
	 *
	 * @var \Sanatorium\Hoofmanager\Repositories\Diseases\DiseasesRepositoryInterface
	 */
	protected $diseases;

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
	 * @param  \Sanatorium\Hoofmanager\Repositories\Diseases\DiseasesRepositoryInterface  $Diseases
	 * @param  \Sanatorium\Hoofmanager\Repositories\Apilog\ApilogRepositoryInterface $apilogs
	 * @return void
	 */
	public function __construct(ApilogRepositoryInterface $apilogs,
		DiseasesRepositoryInterface $diseases)
	{
		parent::__construct($apilogs);

		$this->diseases = $diseases;

		$this->apilogs = $apilogs;
	}

	/**
	 * Datasource for the diseases Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$data = $this->diseases->grid();

		$columns = [
			'id',
			'name',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		$transformer = function($element)
		{
			$element->edit_uri = route('admin.sanatorium.hoofmanager.diseases.edit', $element->id);

			return $element;
		};

		return datagrid($data, $columns, $settings, $transformer);
	}

	public function simple()
	{
		$cols = Input::has('cols') ? Input::get('cols') : ['id', 'name','infectious'];

		$data = $this->diseases->all();
		
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
	 * Show the form for creating new diseases.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating new diseases.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating diseases.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function edit($id)
	{
		return $this->showForm('update', $id);
	}

	/**
	 * Handle posting of the form for updating diseases.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($id)
	{
		return $this->processForm('update', $id);
	}

	/**
	 * Remove the specified diseases.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($id)
	{
		$type = $this->diseases->delete($id) ? 'success' : 'error';

		$this->alerts->{$type}(
			trans("sanatorium/hoofmanager::diseases/message.{$type}.delete")
		);

		return redirect()->route('admin.sanatorium.hoofmanager.diseases.all');
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
				$this->diseases->{$action}($row);
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
		// Do we have a diseases identifier?
		if (isset($id))
		{
			if ( ! $diseases = $this->diseases->find($id))
			{
				$this->alerts->error(trans('sanatorium/hoofmanager::diseases/message.not_found', compact('id')));

				return redirect()->route('admin.sanatorium.hoofmanager.diseases.all');
			}
		}
		else
		{
			$diseases = $this->diseases->createModel();
		}

		// Show the page
		return view('sanatorium/hoofmanager::diseases.form', compact('mode', 'diseases'));
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
		// Store the diseases
		list($messages, $disease) = $this->diseases->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			return $disease;
		}

		return ['success' => false];
	}

}
