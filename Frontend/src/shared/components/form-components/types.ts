export type OptionType = {
  value: string;
  label: string;
  isDisabled?: boolean;
};

export type MultiSelectProps = {
  name: string;
  label?: string;
  options: OptionType[];
  placeholder?: string;
  isMulti?: boolean;
  className?: string;
  isDisabled?: boolean;
  classNamePrefix?: string;
  closeMenuOnSelect?: boolean;
  maxMenuHeight?: number;
  isOptionDisabled?: (option: OptionType) => boolean;
  customError?: string;
  required?: boolean;
};
