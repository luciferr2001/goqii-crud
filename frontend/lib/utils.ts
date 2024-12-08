import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";
import toast, {ToastType } from "react-hot-toast";

const FAKE_TOKEN = "sdfijsoidfjio3jr2o4u294294323rj2foijewjf";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}
export const getRequest = (url: string): Promise<any> => {
  return fetch(url, {
    headers: {
      Authorization: `Bearer ${FAKE_TOKEN}`,
      "Content-Type": "application/json",
    },
  })
    .then((res) => res.json())
    .catch((error) => {
      console.error("Error in getRequest:", error);
      return Promise.resolve(); // Return an empty resolved promise
    });
};

export const postRequest = (url: string, data: any): Promise<any> => {
  return fetch(url, {
    method: "POST",
    headers: {
      Authorization: `Bearer ${FAKE_TOKEN}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((res) => res.json())
    .catch((error) => {
      console.error("Error in postRequest:", error);
      return Promise.resolve(); // Return an empty resolved promise
    });
};

export const patchRequest = (url: string, data: any) => {
  return fetch(url, {
    method: "PATCH",
    headers: {
      Authorization: `Bearer ${FAKE_TOKEN}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  })
    .then((res) => res.json())
    .catch((error) => {
      console.error("Error in patchRequest:", error);
      return Promise.resolve(); // Return an empty resolved promise
    });
};

export const deleteRequest = (url: string) => {
  return fetch(url, {
    method: "DELETE",
    headers: {
      Authorization: `Bearer ${FAKE_TOKEN}`,
      "Content-Type": "application/json",
    },
  })
    .then((res) => res.json())
    .catch((error) => {
      console.error("Error in deleteRequest:", error);
      return Promise.resolve(); // Return an empty resolved promise
    });
};


import { FieldValues } from 'react-hook-form';

export const filterChangedFormFields = <T extends FieldValues>(
    allFields: T,
    dirtyFields: Partial<Record<keyof T, boolean>>
): Partial<T> => {
    const changedFieldValues = Object.keys(dirtyFields).reduce((acc, currentField) => {
        return {
            ...acc,
            [currentField]: allFields[currentField]
        }
    }, {} as Partial<T>);

    return changedFieldValues;
};

export const notify = (message: string, type: ToastType) => {
  if (type === "success") {
    toast.success(message);
  } else if (type === "error") {
    toast.error(message);
  } else {
    toast(message); // Fallback to the default toast type
  }
};

export const HARCODED_URL="http://localhost:8080/v1"