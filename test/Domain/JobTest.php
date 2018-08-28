<?php

use PHPUnit\Framework\TestCase;
use lokothodida\Recruitment\Domain\Job;

class JobTest extends TestCase
{
    public function testJobsOpenToHiringCanBeAppliedFor()
    {
        $job = Job::openForHiring('software-engineer');
        $this->assertFalse($job->applicantHasApplied('applicant-1'));
        $job->apply('applicant-1');
        $this->assertTrue($job->applicantHasApplied('applicant-1'));
    }

    public function testJobsClosedForNewApplicationsCannotBeAppliedFor()
    {
        $this->expectException(DomainException::class);
        $job = Job::openForHiring('software-engineer');
        $job->closeForNewApplications();
        $job->apply('applicant-2');
    }

    public function testApplicantsCanBeHiredForJobsTheyAppliedFor()
    {
        $job = Job::openForHiring('software-engineer');
        $this->assertFalse($job->applicantReceivedOffer('applicant-3'));
        $job->apply('applicant-3');
        $job->offer('applicant-3');
        $this->assertTrue($job->applicantReceivedOffer('applicant-3'));
    }

    public function testApplicantsCannotBeHiredForJobsTheyDidNotApplyFor()
    {
        $this->expectException(DomainException::class);
        $job = Job::openForHiring('software-engineer');
        $job->offer('applicant-4');
    }

    public function testApplicantsCannotBeRejectedForJobsTheyDidNotApplyFor()
    {
        $this->expectException(DomainException::class);
        $job = Job::openForHiring('software-engineer');
        $job->reject('applicant-5');
    }

    public function testApplicantsCannotApplyMoreThanOnceForTheSameJob()
    {
        $this->expectException(DomainException::class);
        $job = Job::openForHiring('software-engineer');
        $job->apply('applicant-6');
        $job->apply('applicant-6');
    }

    public function testApplicantsCannotAcceptJobOffersUntilHired()
    {
        $this->expectException(DomainException::class);
        $job = Job::openForHiring('software-engineer');
        $job->apply('applicant-7');
        $job->acceptOffer('applicant-7');
    }

    public function testApplicantsCannotDeclineJobOffersUntilHired()
    {
        $this->expectException(DomainException::class);
        $job = Job::openForHiring('software-engineer');
        $job->apply('applicant-8');
        $job->declineOffer('applicant-8');
    }

    public function testHiredApplicantsCanAcceptTheJobOffer()
    {
		$job = Job::openForHiring('software-engineer');
        $job->apply('applicant-9');
        $job->offer('applicant-9');
		$this->assertFalse($job->applicantHasBeenHired('applicant-9'));
        $job->acceptOffer('applicant-9');
		$this->assertTrue($job->applicantHasBeenHired('applicant-9'));
    }

    public function testHiredApplicantsCanDeclineTheJobOffer()
    {
		$job = Job::openForHiring('software-engineer');
        $job->apply('applicant-10');
        $job->offer('applicant-10');
        $job->declineOffer('applicant-10');
		$this->assertFalse($job->applicantHasBeenHired('applicant-10'));
    }
}
