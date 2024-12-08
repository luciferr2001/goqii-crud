import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

const FAKE_TOKEN = "sdfijsoidfjio3jr2o4u294294323rj2foijewjf";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export const getRequest = (url: string) => {
  try {
    return fetch(url, {
      headers: {
        "Authorization": `Bearer ${FAKE_TOKEN}`,
        "Content-Type": "application/json"
      }
    }).then((res) => res.json());
  } catch (error) {
    console.error(error);
  }
};

export const postRequest = (url: string, data: any) => {
  try {
    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ data }),
    }).then((res) => res.json());
  } catch (error) {
    console.error(error);
  }
};
