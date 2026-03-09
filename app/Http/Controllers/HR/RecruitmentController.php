<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobVacancy;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\Office;
use App\Models\Plantilla;
use App\Models\SalaryGrade;
use Illuminate\Support\Facades\Auth;

class RecruitmentController extends Controller
{
    // ==================== JOB VACANCIES ====================
    
    public function vacanciesIndex(Request $request)
    {
        $query = JobVacancy::with(['office', 'plantilla', 'salaryGrade']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->office_id) {
            $query->where('office_id', $request->office_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('vacancy_title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $vacancies = $query->orderBy('created_at', 'desc')->paginate(15);
        $offices = Office::active()->orderBy('office_name')->get();

        return view('modules.hr.recruitment.vacancies.index', compact('vacancies', 'offices'));
    }

    public function vacanciesCreate()
    {
        $offices = Office::active()->orderBy('office_name')->get();
        $plantillas = Plantilla::active()->vacant()->with(['office', 'salaryGrade'])->get();
        $salaryGrades = SalaryGrade::active()->orderBy('grade_number')->get();

        return view('modules.hr.recruitment.vacancies.create', compact('offices', 'plantillas', 'salaryGrades'));
    }

    public function vacanciesStore(Request $request)
    {
        $validated = $request->validate([
            'vacancy_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'office_id' => 'required|exists:offices,id',
            'plantilla_id' => 'nullable|exists:plantilla,id',
            'salary_grade_id' => 'nullable|exists:salary_grades,id',
            'number_of_positions' => 'required|integer|min:1',
            'position_level' => 'nullable|string|max:50',
            'qualifications' => 'nullable|string',
            'duties_and_responsibilities' => 'nullable|string',
            'posting_date' => 'nullable|date',
            'closing_date' => 'nullable|date',
            'status' => 'required|in:draft,open,closed,cancelled',
            'remarks' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        
        JobVacancy::create($validated);

        return redirect()->route('hr.recruitment.vacancies.index')
            ->with('success', 'Job vacancy created successfully.');
    }

    public function vacanciesShow(JobVacancy $vacancy)
    {
        $vacancy->load(['office', 'plantilla', 'salaryGrade', 'applicants']);
        return view('modules.hr.recruitment.vacancies.show', compact('vacancy'));
    }

    public function vacanciesEdit(JobVacancy $vacancy)
    {
        $offices = Office::active()->orderBy('office_name')->get();
        $plantillas = Plantilla::active()->with(['office', 'salaryGrade'])->get();
        $salaryGrades = SalaryGrade::active()->orderBy('grade_number')->get();

        return view('modules.hr.recruitment.vacancies.edit', compact('vacancy', 'offices', 'plantillas', 'salaryGrades'));
    }

    public function vacanciesUpdate(Request $request, JobVacancy $vacancy)
    {
        $validated = $request->validate([
            'vacancy_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'office_id' => 'required|exists:offices,id',
            'plantilla_id' => 'nullable|exists:plantilla,id',
            'salary_grade_id' => 'nullable|exists:salary_grades,id',
            'number_of_positions' => 'required|integer|min:1',
            'position_level' => 'nullable|string|max:50',
            'qualifications' => 'nullable|string',
            'duties_and_responsibilities' => 'nullable|string',
            'posting_date' => 'nullable|date',
            'closing_date' => 'nullable|date',
            'status' => 'required|in:draft,open,closed,cancelled',
            'remarks' => 'nullable|string',
        ]);

        $vacancy->update($validated);

        return redirect()->route('hr.recruitment.vacancies.index')
            ->with('success', 'Job vacancy updated successfully.');
    }

    public function vacanciesDestroy(JobVacancy $vacancy)
    {
        $vacancy->delete();
        return redirect()->route('hr.recruitment.vacancies.index')
            ->with('success', 'Job vacancy deleted successfully.');
    }

    public function vacanciesPublish(JobVacancy $vacancy)
    {
        $vacancy->update(['status' => 'open']);
        return back()->with('success', 'Job vacancy published.');
    }

    public function vacanciesClose(JobVacancy $vacancy)
    {
        $vacancy->update(['status' => 'closed']);
        return back()->with('success', 'Job vacancy closed.');
    }

    // ==================== APPLICANTS ====================

    public function applicantsIndex(Request $request)
    {
        $query = Applicant::with(['jobVacancy']);

        if ($request->vacancy_id) {
            $query->where('job_vacancy_id', $request->vacancy_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('application_number', 'like', "%{$request->search}%");
            });
        }

        $applicants = $query->orderBy('created_at', 'desc')->paginate(15);
        $vacancies = JobVacancy::where('status', 'open')->orderBy('vacancy_title')->get();

        return view('modules.hr.recruitment.applicants.index', compact('applicants', 'vacancies'));
    }

    public function applicantsCreate(Request $request)
    {
        $vacancyId = $request->vacancy_id;
        $vacancies = JobVacancy::where('status', 'open')->orderBy('vacancy_title')->get();

        return view('modules.hr.recruitment.applicants.create', compact('vacancies', 'vacancyId'));
    }

    public function applicantsStore(Request $request)
    {
        $validated = $request->validate([
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'civil_status' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:255',
            'work_experience' => 'nullable|string',
            'eligibility' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $validated['application_number'] = Applicant::generateApplicationNumber();
        $validated['application_date'] = now()->toDateString();
        
        $applicant = Applicant::create($validated);

        return redirect()->route('hr.recruitment.applicants.show', $applicant->id)
            ->with('success', 'Applicant registered successfully.');
    }

    public function applicantsShow(Applicant $applicant)
    {
        $applicant->load(['jobVacancy.office', 'documents', 'interviews']);
        return view('modules.hr.recruitment.applicants.show', compact('applicant'));
    }

    public function applicantsEdit(Applicant $applicant)
    {
        $vacancies = JobVacancy::orderBy('vacancy_title')->get();
        return view('modules.hr.recruitment.applicants.edit', compact('applicant', 'vacancies'));
    }

    public function applicantsUpdate(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'civil_status' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:255',
            'work_experience' => 'nullable|string',
            'eligibility' => 'nullable|string|max:255',
            'status' => 'required|in:pending,screening,interview,selected,not_selected,withdrawn',
            'remarks' => 'nullable|string',
        ]);

        $applicant->update($validated);

        return redirect()->route('hr.recruitment.applicants.show', $applicant->id)
            ->with('success', 'Applicant updated successfully.');
    }

    public function applicantsDestroy(Applicant $applicant)
    {
        $applicant->delete();
        return redirect()->route('hr.recruitment.applicants.index')
            ->with('success', 'Applicant deleted successfully.');
    }

    public function applicantsSelect(Applicant $applicant)
    {
        $applicant->update(['status' => 'selected']);
        
        // Optionally close the vacancy if all positions are filled
        $vacancy = $applicant->jobVacancy;
        $filledCount = $vacancy->applicants()->where('status', 'selected')->count();
        
        if ($filledCount >= $vacancy->number_of_positions) {
            $vacancy->update(['status' => 'closed']);
        }

        return back()->with('success', 'Applicant selected for appointment.');
    }

    public function applicantsReject(Applicant $applicant)
    {
        $applicant->update(['status' => 'not_selected']);
        return back()->with('success', 'Applicant marked as not selected.');
    }

    // ==================== INTERVIEWS ====================

    public function interviewsIndex(Request $request)
    {
        $query = Interview::with(['applicant.jobVacancy', 'interviewer']);

        if ($request->status) {
            $query->where('result', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        $interviews = $query->orderBy('scheduled_at')->paginate(15);

        return view('modules.hr.recruitment.interviews.index', compact('interviews'));
    }

    public function interviewsSchedule(Request $request)
    {
        $applicantId = $request->applicant_id;
        $applicant = Applicant::with('jobVacancy')->findOrFail($applicantId);
        
        return view('modules.hr.recruitment.interviews.schedule', compact('applicant'));
    }

    public function interviewsStore(Request $request)
    {
        $validated = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'interview_type' => 'required|string|max:50',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['interviewer_id'] = Auth::id();
        
        $interview = Interview::create($validated);

        // Update applicant status to interview
        Applicant::find($validated['applicant_id'])->update(['status' => 'interview']);

        return redirect()->route('hr.recruitment.applicants.show', $validated['applicant_id'])
            ->with('success', 'Interview scheduled successfully.');
    }

    public function interviewsResult(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'result' => 'required|in:pending,passed,failed,rescheduled,cancelled',
            'rating' => 'nullable|numeric|min:0|max:100',
            'remarks' => 'nullable|string',
        ]);

        $validated['conducted_at'] = now();
        
        $interview->update($validated);

        // Update applicant status based on interview result
        $applicant = $interview->applicant;
        if ($validated['result'] === 'passed') {
            $applicant->update(['status' => 'screening']);
        } elseif ($validated['result'] === 'failed') {
            $applicant->update(['status' => 'not_selected']);
        }

        return back()->with('success', 'Interview result recorded.');
    }

    public function interviewsDestroy(Interview $interview)
    {
        $applicantId = $interview->applicant_id;
        $interview->delete();
        
        // Reset applicant status if no more interviews
        $remainingInterviews = Interview::where('applicant_id', $applicantId)->count();
        if ($remainingInterviews === 0) {
            Applicant::find($applicantId)->update(['status' => 'pending']);
        }

        return back()->with('success', 'Interview deleted.');
    }
}
