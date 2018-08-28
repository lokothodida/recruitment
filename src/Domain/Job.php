<?php

namespace lokothodida\Recruitment\Domain;

use DomainException;

final class Job
{
    private $id;
    private $allowNewApplications = true;
    private $applications = [];

    private const APPLICANT_APPLIED  = 'APPLIED';
    private const APPLICANT_REJECTED = 'REJECTED';
    private const APPLICANT_OFFERED  = 'OFFERED';
    private const APPLICANT_HIRED    = 'HIRED';
    private const APPLICANT_DECLINED = 'DECLINED';

    public static function openForHiring(string $jobId): Job
    {
        return new Job($jobId);
    }

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function apply(string $applicantId): void
    {
        if (!$this->allowNewApplications) {
            throw new DomainException('Job is closed to new applications');
        }

        if (isset($this->applications[$applicantId])) {
            throw new DomainException('This applicant has already applied');
        }

        $this->applications[$applicantId] = self::APPLICANT_APPLIED;
    }

    public function offer(string $applicantId): void
    {
        if (!$this->applicantHasApplied($applicantId)) {
            throw new DomainException('This applicant has not applied');
        }

        $this->applications[$applicantId] = self::APPLICANT_OFFERED;
    }

    public function reject(string $applicantId): void
    {
        if (!$this->applicantHasApplied($applicantId)) {
            throw new DomainException('This applicant has not applied');
        }

        $this->applications[$applicantId] = self::APPLICANT_REJECTED;
    }

    public function acceptOffer(string $applicantId): void
    {
        if (!$this->applicantReceivedOffer($applicantId)) {
            throw new DomainException('This applicant has not been offered a job');
        }

        $this->applications[$applicantId] = self::APPLICANT_HIRED;
    }

    public function declineOffer(string $applicantId): void
    {
        if (!$this->applicantReceivedOffer($applicantId)) {
            throw new DomainException('This applicant has not been offered a job');
        }

        $this->applications[$applicantId] = self::APPLICANT_DECLINED;
    }

    public function applicantHasApplied(string $applicantId): bool
    {
        return isset($this->applications[$applicantId]);
    }

    public function applicantReceivedOffer(string $applicantId): bool
    {
        return $this->applicantHasApplied($applicantId)
        	&& $this->applications[$applicantId] === self::APPLICANT_OFFERED;
    }

    public function applicantHasBeenHired(string $applicantId): bool
    {
        return $this->applicantHasApplied($applicantId)
        	&& $this->applications[$applicantId] === self::APPLICANT_HIRED;
    }

    public function closeForNewApplications(): void
    {
        $this->allowNewApplications = false;
    }
}
