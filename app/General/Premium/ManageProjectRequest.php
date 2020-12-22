<?php

namespace App\General\Premium;

use App\User;
use Carbon\Carbon;
use App\Utilities\Emails\Email;
use App\General\UploadManyFiles;
use App\Marketplace\Studio\Studio;
use Illuminate\Http\JsonResponse;

class ManageProjectRequest
{
    public function create($data)
    {
        /**
         * @depreciated Permit is no longer used, use project payment instead
         */

        // $permit = $studio->permits()->where(['is_consumed' => 0, 'user_id' => \Auth::id()])->first();
        // if (!$permit) {
        //     abort(422, 'PERMIT_REQUIRED');
        // }

        $validPayment = ProjectPayment::where([
            'user_id'            => \Auth::id(),
        ])->whereNull('project_request_id')->find($data['project_payment_id']);

        if (!$validPayment) {
            abort(422, 'PAYMENT_REQUIRED');
        }

        $this->userMustHaveOnlyOnePendingRequestForAStudio($data);

        // @todo check package is from studio
        $package = $validPayment->projectPackage;

        $request       = new ProjectRequest();
        $request->name = $data['name'];
        // $request->message = $data['message'];
        $request->age                    = $data['age'];
        $request->briefing               = $data['briefing'];
        $request->business               = $data['business'];
        $request->color_limit            = $data['color_limit'];
        $request->country                = $data['country'];
        $request->gender                 = $data['gender'];
        $request->number                 = $data['number'];
        $request->other_style            = $data['other'];
        $request->product                = $data['product'];
        $request->style                  = $data['style'];
        $request->tpx                    = $data['tpx'];
        $request->website                = $data['website'];
        $request->user_id                = \Auth::id();
        $request->studio_id              = $data['studio_id'] ?? 1;
        $request->project_package_id     = $validPayment->project_package_id;
        $request->expected_quantity      = $package->expected_quantity;
        $request->status                 = ProjectRequest::IS_WAITING_APPROVAL;

        $request->save();

        $validPayment->project_request_id = $request->id;
        $validPayment->status             = $validPayment::IS_PENDING;
        $validPayment->save();

        if (isset($data['files']) && $data['files']) {
            (new UploadManyFiles($data['files']))->to($request)->save('name');
        }

        $owner = Studio::find($request->studio_id)->user;

        (new Email('project-request-applied'))->send(\Auth::user(), ['project_request_id' => $request->id]);
        // if ($studio = Studio::find($data['studio_id'])) {
        //     (new Email('project-request'))->send(User::find($studio->user_id), ['request_id' => $request->id]);
        // }

        return $request;
    }

    public function userMustHaveOnlyOnePendingRequestForAStudio($data)
    {
        if (ProjectRequest::where('user_id', \Auth::id())->where('studio_id', $data['studio_id'])->where('status', 'IN', [ProjectRequest::IS_WAITING_APPROVAL, 0])->exists()) {
            // abort(422, 'CANNOT_HAVE_MULTIPLE_PENDING_DESIGN_REQUEST');
            return new JsonResponse(['message' => 'CANNOT_HAVE_MULTIPLE_PENDING_DESIGN_REQUEST'], 422);
        }
    }

    public function attach($request, $files)
    {
        (new UploadManyFiles($files))->to($request)->save('name');
        return $request;
    }

    public function start($request, $data = [])
    {
        if (!$request->startable()) {
            abort(422, 'REQUEST_NOT_STARTABLE');
        }

        $uniqueCode = $data['code'] ?? str_random(10);

        if (Project::where('code', $uniqueCode)->exists()) {
            abort(422, 'PROJECT_CODE_MUST_BE_UNIQUE');
        }

        $request->status            = ProjectRequest::IS_STARTED;
        $request->expected_at       = $data['expected_at']       ?? null;
        // studio cannot set quantity
        $request->expected_quantity = $request->projectPackage->expected_quantity;
        $request->save();

        $project                      = $request->project ?: (new Project());
        $project->user_id             = $request->user_id;
        $project->request_id          = $request->id;
        $project->studio_id           = $request->studio_id;
        $project->project_package_id  = $request->project_package_id;
        $project->code                = $uniqueCode;
        $project->status              = Project::IS_STARTED;
        $project->save();

        $project->translations()->save(new ProjectsTranslation(['id' => $project->id, 'name' => $request->name, 'lang' => 'en']));
        $project->translations()->save(new ProjectsTranslation(['id' => $project->id, 'name' => $request->name, 'lang' => 'zh-CN']));

        /**
         * @depreciated use the permit, some studio might not require using permit if 0 price is set
         */

        // $permit              = $project->studio->permits()
        //                         ->where(['is_consumed' => 0, 'user_id' => $project->user_id])->first();
        // if ($permit) {
        //     $permit->is_consumed = 1;
        //     $permit->save();
        // }
        $payment         = $request->payment;
        $payment->status = ProjectPayment::IS_ACCEPTED;
        $payment->save();

        (new Email('project-request-accepted'))->send($request->user, ['project_request_id' => $request->id]);

        return $request;
    }

    /**
     * It readys to show the project to user when all new uploads are done
     */
    public function ready($request, $data = [])
    {
        $project               = $request->project;
        if ($project->items->count() < $request->expected_quantity) {
            abort(422, 'INSUFFICIENT_DESIGNS_QUANTITY');
        }
        if ($request->projectPackage->has_moodboard && !$project->moodBoards->count()) {
            abort(422, 'MISSING_PROJECT_MOODBOARD');
        }
        $request->status     = ProjectRequest::IS_READY;
        $request->save();
        $project->status     = Project::IS_READY;
        $project->expired_at = Carbon::now()->addDays($request->projectPackage->expiry_days)->toDateTimeString();
        $project->save();
        (new Email('project-ready'))->send($project->user, ['project_id' => $project->id]);
        return $request;
    }

    public function decline($request, $data = [])
    {
        if ($request->status !== ProjectRequest::IS_WAITING_APPROVAL) {
            abort(422, 'CANNOT_DECLINE_REQUEST');
        }

        $payment         = $request->payment;

        (new ProjectPaymentManager)->refund($payment);

        // $payment->status = ProjectPayment::IS_REJECTED;
        // $payment->save();

        $request->status  = ProjectRequest::IS_REJECTED;
        $request->reason  = $data['reason'];
        $request->message = $data['message'] ?? null;
        $request->save();

        (new Email('project-request-rejected'))->send($request->user, ['project_request_id' => $request->id]);

        return $request;

        // @todo send email
        // @todo call refund
    }

    public function declineProject($request, $data = [])
    {
        if (!\Auth::user()->is_super_admin) {
            abort(422, 'ADMIN_ONLY');
        }

        $payment = $request->payment;
        (new ProjectPaymentManager)->refund($payment);

        $request->status  = ProjectRequest::IS_REJECTED;
        $request->save();

        $project = $request->project;
        $project->status = Project::IS_REJECTED;
        $project->save();

        return $request;
    }

    public function complete($request, $data = [])
    {
        if (!$request->completable()) {
            abort(422, 'REQUEST_NOT_COMPLETABLE');
        }

        $request->status = ProjectRequest::IS_COMPLETED;
        $request->save();

        $project             = $request->project;
        // $project->expired_at = Carbon::now()->toDateTimeString();
        $project->status     = Project::IS_COMPLETED;
        $project->save();

        // (new Email('project-completed'))->send($request->user, ['project_id' => $project->id]);

        return $request;
    }
}
