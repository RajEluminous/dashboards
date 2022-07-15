<?php

namespace App\Http\Controllers;

use App\Models\CbMasterList;
use App\Models\AffiliateMaster;
use App\Models\PartnerMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class CbMasterListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        // Process Mapping request
        if ($request->all() && !isset($request->page) && isset($request->affiliate)  && isset($request->partner)) {

            // redirect to list
            $request->validate([
                'affiliate' => 'required',
                'partner' => 'required'
            ]);

            $CbMaster = CbMasterList::where([
                'affiliate_id' => $request->affiliate,
                'partner_id' => $request->partner
            ])->first();

            if ($CbMaster === null) {

                if (isset($request->recid) && $request->recid > 0) {

                    CbMasterList::where('id', $request->recid)->update([
                        'affiliate_id' => $request->affiliate,
                        'partner_id' => $request->partner
                    ]);
                    return redirect()->route('cbmaster')->with('success', 'Affiliate - Partner record updated successfully.');
                } else {
                    CbMasterList::insert([
                        'affiliate_id' => $request->affiliate,
                        'partner_id' => $request->partner
                    ]);
                    return redirect()->route('cbmaster')->with('success', 'Affiliate assigned to Partner successfully.');
                }
            } else {
                return redirect()->route('cbmaster')->with('error', 'Provided Affiliate - Partner already exist.');
            }
        }

        $limitRec = 10;
        $cntr = 0;
        if (isset($request->page)) {
            $cntr = $request->page * $limitRec - $limitRec;
        }

        list($affArry, $partArry) = $this->getCBMasterName();

        if (isset($request->fAffiliate)) {
            $cbmasterlist = CbMasterList::where('affiliate_id', $request->fAffiliate)->paginate($limitRec);
        } else if (isset($request->fPartner)) {
            $cbmasterlist = CbMasterList::where('partner_id', $request->fPartner)->paginate($limitRec);
        } else {
            $cbmasterlist = CbMasterList::orderBy('id', 'desc')->paginate($limitRec);
        }

        //dd('test');
        $cbArr = array();
        $counter = $cntr + 1;
        foreach ($cbmasterlist as $rs) {

            $cbArr[] = array(
                'id' => $rs->id,
                'aff_id' => $rs->affiliate_id,
                'affiliate_id' => $affArry[$rs->affiliate_id],
                'part_id' => $rs->partner_id,
                'partner_id' => $partArry[$rs->partner_id],
                'status' => $rs->status,
                'count' => $counter
            );
            $counter++;
        }

        return view('cbmaster.index', compact('cbArr', 'affArry', 'partArry', 'cbmasterlist'));
    }



    public function create_affiliate(Request $request)
    {

        // Process request
        if ($request->all() && !isset($request->page) && $request->submit != 'Search') {

            // redirect to list
            $request->validate([
                'name' => 'required' /*|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/*/
            ]);

            $aff = AffiliateMaster::where('name', '=', $request->name)->first();
            if ($aff === null) {

                if (isset($request->recid) && $request->recid > 0) {
                    AffiliateMaster::where('id', $request->recid)->update([
                        'name' => $request->name
                    ]);
                    return redirect('/cb-master/create_affiliate')->with('success', 'Affiliate record updated successfully.');
                } else {
                    AffiliateMaster::create($request->all());
                    return redirect('/cb-master/create_affiliate')->with('success', 'Affiliate added successfully.');
                }
            } else {
                return redirect('/cb-master/create_affiliate')->with('error', 'Affiliate already exist.');
            }
        }

        $cbmasterlist = CbMasterList::orderBy('id', 'desc')->get();
        $cbArr = array();
        foreach ($cbmasterlist as $af) {
            $cbArr[] = $af->affiliate_id;
        }

        // Get list of Affiliates
        $limitRec = 10;
        $cntr = 0;
        if (isset($request->page)) {
            $cntr = $request->page * $limitRec - $limitRec;
        }

        if (isset($_POST['submit']) && $request->submit == 'Search')
            $affiliatelist = AffiliateMaster::where('name', 'like', '%' . $request->name . '%')->paginate($limitRec);
        else
            $affiliatelist = AffiliateMaster::orderBy('name', 'asc')->paginate($limitRec);

        $affArr = array();
        $counter = $cntr + 1;
        foreach ($affiliatelist as $rs) {

            $isInMasterList = false;
            if (!isset($cbArr[$rs->id])) {
                $isInMasterList = true;
            }

            $affArr[] = array(
                'id' => $rs->id,
                'name' => $rs->name,
                'count' => $counter,
                'isInMasterList' => $isInMasterList
            );
            $counter++;
        }

        return view('cbmaster.create_affiliate', compact('affArr', 'affiliatelist'));
    }

    public function create_partner(Request $request)
    {
        // echo $request->submit;
        //  dd($request->all());
        // Process request
        if ($request->all() && !isset($request->page) && $request->submit != 'Search') {

            $request->validate([
                'name' => 'required' /*|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/*/
            ]);

            $part = PartnerMaster::where('name', '=', $request->name)->first();
            if ($part === null) {

                if (isset($request->recid) && $request->recid > 0) {
                    PartnerMaster::where('id', $request->recid)->update([
                        'name' => $request->name
                    ]);
                    return redirect('/cb-master/create_partner')->with('success', 'Partner updated successfully.');
                } else {
                    PartnerMaster::create($request->all());
                    return redirect('/cb-master/create_partner')->with('success', 'Partner added successfully.');
                }
            } else {
                return redirect('/cb-master/create_partner')->with('error', 'Partner already exist.');
            }
        }

        $cbmasterlist = CbMasterList::orderBy('id', 'desc')->get();
        $cbArr = array();
        foreach ($cbmasterlist as $af) {
            $cbArr[] = $af->partner_id;
        }

        $limitRec = 10;
        $cntr = 0;
        if (isset($request->page)) {
            $cntr = $request->page * $limitRec - $limitRec;
        }

        if (isset($_POST['submit']) && $request->submit == 'Search')
            $partnerlist = PartnerMaster::where('name', 'like', '%' . $request->name . '%')->paginate($limitRec);
        else
            $partnerlist = PartnerMaster::orderBy('name', 'asc')->paginate($limitRec);
        $partArr = array();
        $counter = $cntr + 1;
        foreach ($partnerlist as $rs) {

            $isInMasterList = false;
            if (!in_array($rs->id, $cbArr)) {
                $isInMasterList = true;
            }

            $partArr[] = array(
                'id' => $rs->id,
                'name' => $rs->name,
                'count' => $counter,
                'isInMasterList' => $isInMasterList
            );
            $counter++;
        }

        return view('cbmaster.create_partner', compact('partArr', 'partnerlist'));
    }

    public function store(Request $request)
    {

        // dd($request->all());

    }

    public function getCBMasterName()
    {

        $aff_list = AffiliateMaster::orderBy('name', 'asc')->get();
        $affArry = array();
        foreach ($aff_list as $rsAff) {
            $affArry[$rsAff->id] = $rsAff->name;
        }

        $part_list = PartnerMaster::orderBy('name', 'asc')->get();
        $partArry = array();
        foreach ($part_list as $rsPart) {
            $partArry[$rsPart->id] = $rsPart->name;
        }
        return array($affArry, $partArry);
    }

    public function destroy($id)
    {
        // dd($id);
        $coronacase = CbMasterList::findOrFail($id);
        $coronacase->delete();

        return redirect()->route('cbmaster')->with('success', 'Affiliate - Partner record is successfully deleted.');
    }

    public function deleteaaffiliate($id)
    {

        $coronacase = AffiliateMaster::findOrFail($id);
        $coronacase->delete();

        return redirect('cb-master/create_affiliate')->with('success', 'Affiliate record is successfully deleted.');
    }

    public function deleteapartner($id)
    {

        $coronacase = PartnerMaster::findOrFail($id);
        $coronacase->delete();

        return redirect('cb-master/create_partner')->with('success', 'Partner record is successfully deleted.');
    }
}
