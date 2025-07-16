import { useState, useMemo } from "react";
import { useAppSelector } from "../store/store";

const multiplierOptions = [1, 2, 3, 4, 5];

export default function MoneyPoolSetup() {
  const employees = useAppSelector((state) => state.employees.employees);
  const paidCount = employees.filter((e) => e.paid).length;

  const [amountPerPerson, setAmountPerPerson] = useState(0);
  const [multiplier, setMultiplier] = useState(1);

  const totalCollected = useMemo(() => paidCount * amountPerPerson, [paidCount, amountPerPerson]);
  const companyContribution = useMemo(() => totalCollected * multiplier, [totalCollected, multiplier]);
  const finalPool = useMemo(() => totalCollected + companyContribution, [totalCollected, companyContribution]);

  return (
    <div className="w-full max-w-xl mx-auto bg-white/80 shadow-xl rounded-3xl p-10 mt-12 mb-12 border border-gray-100">
      <h1 className="text-3xl font-extrabold mb-10 text-center text-gray-900 tracking-tight">Money Pool Setup</h1>
      <div className="space-y-8">
        <div>
          <label className="block text-gray-700 font-medium mb-2">Amount collected per person</label>
          <input
            type="number"
            min={0}
            className="w-full px-5 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
            value={amountPerPerson}
            onChange={e => setAmountPerPerson(Number(e.target.value))}
            placeholder="Enter amount (e.g. 100)"
          />
        </div>
        <div>
          <label className="block text-gray-700 font-medium mb-2">Company contribution multiplier</label>
          <select
            className="w-full px-5 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition"
            value={multiplier}
            onChange={e => setMultiplier(Number(e.target.value))}
          >
            {multiplierOptions.map(opt => (
              <option key={opt} value={opt}>{opt}x</option>
            ))}
          </select>
        </div>
        <div className="bg-gray-50 rounded-xl p-6 flex flex-col gap-4 border border-gray-100">
          <div className="flex justify-between text-gray-700">
            <span>Total collected from employees</span>
            <span className="font-semibold text-gray-900">₹ {totalCollected.toLocaleString()}</span>
          </div>
          <div className="flex justify-between text-gray-700">
            <span>Company contribution</span>
            <span className="font-semibold text-blue-700">₹ {companyContribution.toLocaleString()}</span>
          </div>
          <div className="flex justify-between text-gray-700 border-t border-gray-200 pt-4 mt-2">
            <span>Final pool amount</span>
            <span className="font-bold text-green-700 text-lg">₹ {finalPool.toLocaleString()}</span>
          </div>
          <div className="flex justify-between text-gray-500 text-sm pt-2">
            <span>Paid employees</span>
            <span className="font-medium text-gray-700">{paidCount}</span>
          </div>
        </div>
        <button
          className="w-full bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-8 py-3 rounded-xl shadow-md hover:from-blue-600 hover:to-indigo-600 transition-all font-semibold text-base focus:outline-none focus:ring-2 focus:ring-blue-300 mt-4"
          onClick={() => alert('Proceeding to Snack Planning (not implemented)')}
        >
          Proceed to Snack Planning
        </button>
      </div>
    </div>
  );
}
