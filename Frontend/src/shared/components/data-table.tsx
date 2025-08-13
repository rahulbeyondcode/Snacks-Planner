import React from "react";

export type TableColumn<T> = {
  key: keyof T | string;
  title: string;
  render?: (value: any, item: T, index: number) => React.ReactNode;
  className?: string;
};

export type TableAction<T> = {
  icon: React.ReactNode;
  onClick: (item: T) => void;
  className?: string;
  title?: string;
};

type DataTableProps<T> = {
  data: T[];
  columns: TableColumn<T>[];
  actions?: TableAction<T>[];
  className?: string;
  headerClassName?: string;
  rowClassName?: string;
};

const DataTable = <T extends Record<string, any>>({
  data,
  columns,
  actions = [],
  className = "w-full overflow-x-auto bg-white rounded-2xl border-2 border-black shadow-[6px_6px_0_0_#000]",
  headerClassName = "bg-yellow-200 border-b-2 border-black",
  rowClassName = "border-b-2 border-black last:border-b-0 hover:bg-yellow-50",
}: DataTableProps<T>) => {
  const getNestedValue = (obj: T, path: string): any => {
    return path.split(".").reduce((current, key) => current?.[key], obj);
  };

  return (
    <div className={className}>
      <table className="w-full min-w-full bg-white text-sm">
        <thead>
          <tr className={headerClassName}>
            {columns.map((column, index) => (
              <th
                key={index}
                className={`py-2.5 px-3 text-left font-extrabold text-black ${column.className || ""}`}
              >
                {column.title}
              </th>
            ))}
            {actions.length > 0 && (
              <th className="py-2.5 px-3 text-center font-extrabold text-black">
                Actions
              </th>
            )}
          </tr>
        </thead>
        <tbody>
          {data.map((item, itemIndex) => (
            <tr key={itemIndex} className={rowClassName}>
              {columns.map((column, colIndex) => (
                <td
                  key={colIndex}
                  className="py-2.5 px-3 text-black align-middle"
                >
                  {column.render
                    ? column.render(
                        getNestedValue(item, column.key as string),
                        item,
                        itemIndex
                      )
                    : getNestedValue(item, column.key as string)}
                </td>
              ))}
              {actions.length > 0 && (
                <td className="py-2 px-3 text-center">
                  <div className="inline-flex items-center justify-center gap-2">
                    {actions.map((action, actionIndex) => (
                      <button
                        key={actionIndex}
                        onClick={() => action.onClick(item)}
                        className={`${action.className || "hover:opacity-75"} inline-flex items-center justify-center w-8 h-8 cursor-pointer rounded-md border-2 border-black bg-yellow-300 text-black font-extrabold shadow-[2px_2px_0_0_#000] hover:bg-yellow-400`}
                        title={action.title}
                      >
                        {action.icon}
                      </button>
                    ))}
                  </div>
                </td>
              )}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default DataTable;
