<?php

use App\Models\Contract;
use App\Models\Department;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new class extends Component
{
    use WithoutUrlPagination , WithPagination;
    public $search = '';

    public function delete($id)
    {
        Contract::find($id)->delete();
        session()->flash('message', 'Contract deleted successfully.');
    }

    public function getContractsProperty()
    {
        return Contract::inCompany()->scopeSearch($this->search)->latest()->paginate(10);
    }
};
?>

<div>
    {{-- Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant --}}
</div>