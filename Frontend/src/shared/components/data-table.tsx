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
  className = "overflow-x-auto rounded-lg border border-red-200",
  headerClassName = "bg-red-100",
  rowClassName = "border-b last:border-b-0",
}: DataTableProps<T>) => {
  const getNestedValue = (obj: T, path: string): any => {
    return path.split(".").reduce((current, key) => current?.[key], obj);
  };

  return (
    <div className={className}>
      <table className="min-w-full bg-white">
        <thead>
          <tr className={headerClassName}>
            {columns.map((column, index) => (
              <th
                key={index}
                className={`py-2 px-3 text-left font-semibold ${column.className || ""}`}
              >
                {column.title}
              </th>
            ))}
            {actions.length > 0 && (
              <th
                className="py-2 px-3 text-center font-semibold"
                colSpan={actions.length}
              >
                Actions
              </th>
            )}
          </tr>
        </thead>
        <tbody>
          {data.map((item, itemIndex) => (
            <tr key={itemIndex} className={rowClassName}>
              {columns.map((column, colIndex) => (
                <td key={colIndex} className="py-2 px-3 font-handwriting">
                  {column.render
                    ? column.render(
                        getNestedValue(item, column.key as string),
                        item,
                        itemIndex
                      )
                    : getNestedValue(item, column.key as string)}
                </td>
              ))}
              {actions.map((action, actionIndex) => (
                <td key={actionIndex} className="py-2 px-3 text-center">
                  <button
                    onClick={() => action.onClick(item)}
                    className={action.className || "hover:opacity-75"}
                    title={action.title}
                  >
                    {action.icon}
                  </button>
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default DataTable;
