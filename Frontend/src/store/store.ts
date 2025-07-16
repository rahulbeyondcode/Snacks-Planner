import { useDispatch, useSelector } from 'react-redux'
import { configureStore } from "@reduxjs/toolkit";

import employeeReducer from "./employeeSlice";

const store = configureStore({
  reducer: {
    employees: employeeReducer,
  },
});

export default store;

// Inferred type: {posts: PostsState, comments: CommentsState, users: UsersState}
type AppDispatch = typeof store.dispatch
// Infer the `RootState` and `AppDispatch` types from the store itself
type RootState = ReturnType<typeof store.getState>

// Use throughout your app instead of plain `useDispatch` and `useSelector`
export const useAppDispatch = useDispatch.withTypes<AppDispatch>()
export const useAppSelector = useSelector.withTypes<RootState>()