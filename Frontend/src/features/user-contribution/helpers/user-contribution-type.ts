export type EmployeeContributionType = {
  contribution_id: number;
  user_id: number;
  user_name: string;
  status: "paid" | "unpaid";
  pending_status?: "paid" | "unpaid";
};

export type ContributionStoreType = {
  contributions: {
    [user_id: string]: EmployeeContributionType;
  };
  contribution_status: {
    paid: Set<number>;
    unpaid: Set<number>;
  };
};

// Backend POST API body type
export type BulkUpdatePayloadType = {
  contributors: number[];
};

// Backend GET API response type
export type ContributionListType = {
  contributions: {
    contribution_id: number;
    user_id: number;
    user_name: string;
    status: boolean;
  }[];
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
