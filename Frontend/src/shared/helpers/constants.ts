// 60000 = 1 minute (1000 * 60)

// Stale Time (Time after which the data is considered stale and a new request is made)
export const GET_MONEY_POOL_STALE_TIME = 5 * 60000; // 5 minutes
export const GET_EMPLOYEE_LIST_STALE_TIME = 5 * 60000; // 5 minutes

// Retry Count (Number of times to retry the request if it fails)
export const GET_MONEY_POOL_RETRY = 3;
export const GET_EMPLOYEE_LIST_RETRY = 3;
