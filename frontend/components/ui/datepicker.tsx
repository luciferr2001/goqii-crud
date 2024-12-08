"use client"

import * as React from "react"
import { format } from "date-fns"
import { CalendarIcon } from "lucide-react"

import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Calendar } from "@/components/ui/calendar"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"

export function DatePicker({
    selectedDate,
    onDateChange,
  }: {
    selectedDate?: Date;
    onDateChange: (date: Date | undefined) => void;
  }) {
    const [date, setDate] = React.useState<Date | undefined>(selectedDate);
  
    React.useEffect(() => {
      setDate(selectedDate);
    }, [selectedDate]);
  
    const handleDateChange = (newDate: Date | undefined) => {
      setDate(newDate);
      onDateChange(newDate);
    };
  
    return (
      <Popover>
        <PopoverTrigger asChild>
          <Button
            variant={"outline"}
            className={cn(
              "w-[240px] justify-start text-left font-normal",
              !date && "text-muted-foreground"
            )}
          >
            <CalendarIcon />
            {date ? format(date, "PPP") : <span>Pick a date</span>}
          </Button>
        </PopoverTrigger>
        <PopoverContent className="w-auto p-0" align="start">
          <Calendar
            mode="single"
            selected={date}
            onSelect={handleDateChange}
            initialFocus
          />
        </PopoverContent>
      </Popover>
    );
  }
  
