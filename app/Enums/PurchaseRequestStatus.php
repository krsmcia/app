<?php

namespace App\Enums;

enum PurchaseRequestStatus: int
{
    case Draft = 1;
    case Submitted = 2;
    case SupervisorApproved = 3;
    case AuditApproved = 4;
    case ManagerApproved = 5;
    case Rejected = 6;
}