import React from "react";

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  children: React.ReactNode;
  className?: string;
  shouldUseDefaultClass?: boolean;
}

const defaultClasses =
  "px-4 py-2 rounded bg-blue-600 text-white font-semibold cursor-pointer hover:bg-blue-700 focus:outline-none transition-colors duration-200 disabled:opacity-60 disabled:cursor-not-allowed";

const Button: React.FC<ButtonProps> = ({
  onClick,
  disabled = false,
  className = "",
  children,
  shouldUseDefaultClass = true,
  ...rest
}) => {
  return (
    <button
      type="button"
      onClick={onClick}
      disabled={disabled}
      className={`${shouldUseDefaultClass ? defaultClasses : ""} ${className}`}
      {...rest}
    >
      {children}
    </button>
  );
};

export default Button;
