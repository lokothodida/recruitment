<?php

namespace lokothodida\Recruitment;

use lokothodida\Recruitment\Domain\JobRepository;
use lokothodida\Recruitment\Domain\Job;

final class App
{
    /** @var JobRepository */
    private $jobs;

    public function __construct(JobRepository $jobs)
    {
        $this->jobs = $jobs;
    }

    public function openJobForHiring(string $jobId): string
    {
        $job = Job::openForHiring($jobId);
        $this->jobs->save($job);

        return $job->id();
    }

    public function applyForJob(string $jobId, string $applicantId): void
    {
        $this->withJob($jobId, function (Job $job) use ($applicantId) {
            $job->apply($applicantId);
        });
    }

    public function offerJobToApplicant(string $jobId, string $applicantId): void
    {
        $this->withJob($jobId, function (Job $job) use ($applicantId) {
            $job->offer($applicantId);
        });
    }

    public function rejectApplicant(string $jobId, string $applicantId): void
    {
        $this->withJob($jobId, function (Job $job) use ($applicantId) {
            $job->reject($applicantId);
        });
    }

    public function acceptJobOffer(string $jobId, string $applicantId): void
    {
        $this->withJob($jobId, function (Job $job) use ($applicantId) {
            $job->acceptOffer($applicantId);
        });
    }

    public function declineJobOffer(string $jobId, string $applicantId): void
    {
        $this->withJob($jobId, function (Job $job) use ($applicantId) {
            $job->declineOffer($applicantId);
        });
    }

    public function closeJobForNewApplications(string $jobId): void
    {
        $this->withJob($jobId, function (Job $job) {
            $job->closeForNewApplications();
        });
    }

    private function withJob(string $jobId, callable $action): void
    {
        $job = $this->jobs->findById($jobId);
        $action($job);
        $this->jobs->save($job);
    }
}
