<?php

namespace lokothodida\Recruitment\Domain;

interface JobRepository
{
    public function findById(string $jobId): Job;
    public function save(Job $job): void;
}
