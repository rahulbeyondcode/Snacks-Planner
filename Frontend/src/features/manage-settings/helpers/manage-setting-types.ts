export type ShopFormDataType = {
  name: string;
  address: string;
  contactDetails: string;
  paymentMode: string;
  notes: string;
};

export type SnackFormDataType = {
  name: string;
  categoryId: string;
  price: number;
  notes?: string;
};

export type CategoryFormDataType = {
  name: string;
};

export type NoSnackDayFormDataType = {
  holidayName: string;
  date: Date | null;
};

export type PaymentModeFormDataType = {
  name: string;
};

type IdType = {
  id: string;
};

export type ShopType = ShopFormDataType & IdType;

export type SnackType = {
  id: string;
  name: string;
  shop: string; // Shop name for display
  category: string; // This is for backwards compatibility with existing data
  pricePerPiece: string; // This is for backwards compatibility with existing data
};

export type CategoryType = CategoryFormDataType & IdType;

export type NoSnackDayType = NoSnackDayFormDataType & IdType;

export type PaymentModeType = PaymentModeFormDataType & IdType;
