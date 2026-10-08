"use client"

import * as React from "react"
import { Slot } from "@radix-ui/react-slot"
import { cva } from "class-variance-authority"

import { cn } from "@/lib/utils"

const buttonVariantsOuter = cva("", {
  variants: {
    variant: {
      primary:
        "w-full border border-[1px] dark:border-[2px] border-emerald-900/20 dark:border-emerald-950 bg-gradient-to-b from-emerald-600/30 to-emerald-950 dark:from-emerald-400/20 dark:to-emerald-950 p-[1px] rounded-[12px] transition duration-300 ease-in-out shadow-sm",
      accent:
        "w-full border-[1px] dark:border-[2px] border-black/10 dark:border-neutral-950 bg-gradient-to-b from-indigo-300/90 to-indigo-500 dark:from-indigo-200/70 dark:to-indigo-500 p-[1px] rounded-[12px] transition duration-300 ease-in-out",
      destructive:
        "w-full border-[1px] dark:border-[2px] border-black/10 dark:border-neutral-950 bg-gradient-to-b from-red-300/90 to-red-500 dark:from-red-300/90 dark:to-red-500 p-[1px] rounded-[12px] transition duration-300 ease-in-out",
      secondary:
        "w-full border-[1px] dark:border-[2px] border-black/20 bg-white/50 dark:border-neutral-950 dark:bg-neutral-600/50 p-[1px] rounded-[12px] transition duration-300 ease-in-out",
      minimal:
        "group w-full border-[1px] dark:border-[2px] border-black/20 bg-white/50 dark:border-neutral-950 dark:bg-neutral-600/80 p-[1px] rounded-[12px] active:bg-neutral-200 dark:active:bg-neutral-800 hover:bg-gradient-to-t hover:from-neutral-100 to-white dark:hover:from-neutral-600/50 dark:hover:to-neutral-600/70",
      icon: "group rounded-full border dark:border-neutral-950 border-black/10 dark:bg-neutral-600/50 bg-white/50 p-[1px] active:bg-neutral-200 dark:active:bg-neutral-800 hover:bg-gradient-to-t hover:from-neutral-100 to-white dark:hover:from-neutral-700 dark:hover:to-neutral-600",
    },
    size: {
      sm: "rounded-[8px]",
      default: "rounded-[12px]",
      lg: "rounded-[14px]",
      icon: "rounded-full",
    },
  },
  defaultVariants: {
    variant: "primary",
    size: "default",
  },
})

const innerDivVariants = cva(
  "w-full h-full flex items-center justify-center font-semibold font-sans transition duration-200 ease-in-out",
  {
    variants: {
      variant: {
        primary:
          "gap-2 bg-gradient-to-b from-emerald-700 via-emerald-800 to-green-950 text-sm text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.35),inset_0_-1px_0_rgba(0,0,0,0.3)] hover:from-emerald-600 hover:to-emerald-800 active:from-green-950 active:to-emerald-950 active:shadow-[inset_0_2px_4px_rgba(0,0,0,0.4)]",
        accent:
          "gap-2 bg-gradient-to-b from-indigo-400 to-indigo-600 text-sm text-white/90 shadow-[inset_0_1px_0_rgba(255,255,255,0.35)] hover:from-indigo-400/80 hover:to-indigo-600/80 active:from-indigo-600 active:to-indigo-700",
        destructive:
          "gap-2 bg-gradient-to-b from-red-500 to-red-700 text-sm text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.35)] hover:from-red-400 hover:to-red-600 active:from-red-700 active:to-red-800",
        secondary:
          "gap-2 bg-gradient-to-b from-neutral-50 to-neutral-200 dark:from-neutral-800 dark:to-neutral-700 text-sm text-neutral-800 dark:text-neutral-100 shadow-[inset_0_1px_0_rgba(255,255,255,0.8)] dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.15)] hover:from-white hover:to-neutral-100 active:from-neutral-200 active:to-neutral-300",
        minimal:
          "gap-2 bg-gradient-to-b from-white to-neutral-50 dark:from-neutral-800 dark:to-neutral-700 text-sm text-neutral-700 dark:text-neutral-200 hover:from-neutral-50 hover:to-neutral-100",
        icon: "bg-gradient-to-b from-white to-neutral-100 dark:from-neutral-800 dark:to-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-full",
      },
      size: {
        sm: "text-xs rounded-[6px] px-3 py-1.5",
        default: "text-sm rounded-[10px] px-4 py-2.5",
        lg: "text-base rounded-[12px] px-6 py-3",
        icon: "rounded-full p-2",
      },
    },
    defaultVariants: {
      variant: "primary",
      size: "default",
    },
  }
)

export interface UnifiedButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?:
    | "primary"
    | "secondary"
    | "accent"
    | "destructive"
    | "minimal"
    | "icon"
  size?: "default" | "sm" | "lg" | "icon"
  asChild?: boolean
}

const TextureButton = React.forwardRef<HTMLButtonElement, UnifiedButtonProps>(
  (
    {
      children,
      variant = "primary",
      size = "default",
      asChild = false,
      className,
      ...props
    },
    ref
  ) => {
    const Comp = asChild ? Slot : "button"

    return (
      <Comp
        className={cn(buttonVariantsOuter({ variant, size }), className)}
        ref={ref}
        {...props}
      >
        <div className={cn(innerDivVariants({ variant, size }))}>
          {children}
        </div>
      </Comp>
    )
  }
)

TextureButton.displayName = "TextureButton"

export { TextureButton }
