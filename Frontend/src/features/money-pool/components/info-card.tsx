import React from "react";

type DataType = string | number | React.ReactNode;

type InfoRowData = {
  label: DataType;
  value: DataType;
};

type InfoCardProps = {
  title: DataType;
  data: InfoRowData[];
  className?: string;
};

const InfoCard: React.FC<InfoCardProps> = ({ title, data, className = "" }) => {
  return (
    <div
      className={`bg-white border-2 border-black shadow-[4px_4px_0_0_#000] rounded-xl p-4 ${className}`}
    >
      <div className="font-extrabold text-black mb-2">{title}</div>
      <div className="grid grid-cols-2 gap-2 text-sm">
        {data.map((item, index) => (
          <React.Fragment key={index}>
            <div className="text-black/70">{item.label}</div>
            <div className="text-right font-extrabold">{item.value}</div>
          </React.Fragment>
        ))}
      </div>
    </div>
  );
};

export default InfoCard;
