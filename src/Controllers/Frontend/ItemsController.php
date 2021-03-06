<?php namespace Sanatorium\Hoofmanager\Controllers\Frontend;

use Platform\Foundation\Controllers\Controller;
use Sanatorium\Hoofmanager\Models\Vet;
use Sanatorium\Hoofmanager\Models\House;
use Sanatorium\Hoofmanager\Models\Item;
use Sanatorium\Hoofmanager\Models\Examination;
use Sentinel;

class ItemsController extends Controller {

    /**
     * Return the main view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $vet = Vet::getVet();

        return view('sanatorium/hoofmanager::index', compact('vet'));
    }

    public function edit($id)
    {

        if ( !Sentinel::check() )
            return redirect()->route('user.login');

        $vet = Vet::getVet();

        $items = app('sanatorium.hoofmanager.items');

        $findings = app('sanatorium.hoofmanager.finding')->where('item_id', $id)->orderBy('created_at', 'DESC')->get();

        if ( $vet->isAdmin() ) {

            $houses = House::all();

        } else {

            $houses = House::where('user_id', $vet->id)->get();

        }

        $diseases = app('sanatorium.hoofmanager.diseases')->findAll();

        $treatments = app('sanatorium.hoofmanager.treatment')->findAll();

        $house = $items->find($id)->houses()->first();

        if ( $id )
        {
            $item = $items->find($id);
        } else
        {
            $item = $items->createModel();
        }

        return view('sanatorium/hoofmanager::items/detail', compact('item', 'findings', 'houses', 'house', 'diseases', 'treatments', 'vet'));
    }

    public function update($id)
    {

        $this->items = app('sanatorium.hoofmanager.items');

        $this->findings = app('sanatorium.hoofmanager.finding');

        if ( request()->finding_id ) {

            list($mesages, $finding) = $this->findings->store(request()->finding_id, request()->except('finding_id'));

        } else {

            list($messages, $item) = $this->items->store($id, request()->except('cattle_id', 'breed'));

        }

        /*if ( request()->cattle_id ) {

            $new_cattle = House::find( request()->cattle_id );

            $item->houses()->sync([$new_cattle->id]);

        } else if ( $item->collar != request()->collar ) {

            dd($item->collar, request()->collar);

            $items->store($id, request()->only('collar'));

        } else if ( $item->birthday != request()->birthday ) {

            $items->store($id, request()->only('birthday'));

        } else {

            $findings->store(request()->finding_id, request()->except('finding_id'));

        }*/

        return redirect()->back();

    }

    public function newfinding($id)
    {
        $data = request()->newfinding[0];

        $vet = Vet::getVet();

        $findings = app('sanatorium.hoofmanager.finding');

        $new_finding = $data;

        $new_finding['user_id'] = $vet->id;

        $new_finding['item_id'] = $id;

        list($messages, $finding) = $findings->store(null, $new_finding);

        return redirect()->back();

    }

    /**
     * Processes the form.
     *
     * @param  string  $mode
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function processFormItem($mode, $id = null)
    {
        $this->items = app('sanatorium.hoofmanager.items');

        // Store the items
        list($messages) = $this->items->store($id, request()->all());

        // Do we have any errors?
        if ($messages->isEmpty())
        {
            $this->alerts->success(trans("sanatorium/hoofmanager::houses/message.success.{$mode}"));

            return redirect()->back();
        }

        $this->alerts->error($messages, 'form');

        return redirect()->back()->withInput();
    }

    protected function processFormFinding($mode, $id = null)
    {

        $this->findings = app('sanatorium.hoofmanager.finding');

        $finding_id = request()->input('finding_id');

        // Store the finding
        list($messages) = $this->findings->store($finding_id, request()->except('finding_id'));

        // Do we have any errors?
        if ($messages->isEmpty())
        {
            $this->alerts->success(trans("sanatorium/hoofmanager::houses/message.success.{$mode}"));

            return redirect()->back();
        }

        $this->alerts->error($messages, 'form');

        return redirect()->back()->withInput();
    }

}