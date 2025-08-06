export type ShopFormDataType = {
  name: string;
  address: string;
  contactDetails: string;
  paymentMode: string;
  notes: string;
};

export type SnackFormDataType = {
  name: string;
  shop: string;
  pricePerPiece: string;
  category: string;
};

export type CategoryFormDataType = {
  name: string;
};

export type NoSnackDayFormDataType = {
  holidayName: string;
  date: string;
};

type IdType = {
  id: string;
};

export type ShopType = ShopFormDataType & IdType;

export type SnackType = SnackFormDataType & IdType;

export type CategoryType = CategoryFormDataType & IdType;

export type NoSnackDayType = NoSnackDayFormDataType & IdType;
