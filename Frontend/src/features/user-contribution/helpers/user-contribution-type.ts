export type EmployeeContribution = {
  contribution_id: number;
  user_id: number;
  user_name: string;
  status: boolean;
  // Local state for tracking pending changes
  pendingStatus?: boolean;
  hasUnsavedChanges?: boolean;
};

export type ContributionResponse = {
  success: boolean;
  data: {
    contributions: EmployeeContribution[];
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      path: string;
      per_page: number;
      to: number;
      total: number;
    };
    paid_contributions: number;
    unpaid_records: number;
  };
};

export type BulkUpdatePayload = {
  contributors: number[];
};

export type BulkUpdateResponse = {
  success: boolean;
  message: string;
  data: {
    contributions: EmployeeContribution[];
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      path: string;
      per_page: number;
      to: number;
      total: number;
    };
    updated_count: number;
    paid_contributions: number;
    unpaid_records: number;
  };
};
