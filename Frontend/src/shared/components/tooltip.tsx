import * as TooltipPrimitive from "@radix-ui/react-tooltip";
import type { ReactNode } from "react";
import React from "react";

export type TooltipPosition = "top" | "bottom" | "left" | "right";

interface TooltipProps {
  content: string /** The content to display inside the tooltip */;
  position?: TooltipPosition /** Position of the tooltip relative to the trigger element */;
  children: ReactNode /** The element that triggers the tooltip on hover */;
  className?: string /** Additional CSS classes for the trigger wrapper */;
  delayDuration?: number /** Delay before showing tooltip (in ms) */;
  disabled?: boolean /** Whether the tooltip is disabled */;
}

const Tooltip: React.FC<TooltipProps> = ({
  content,
  position = "top",
  children,
  className = "",
  delayDuration = 200,
  disabled = false,
}) => {
  if (disabled) {
    return <>{children}</>;
  }

  return (
    <TooltipPrimitive.Provider delayDuration={delayDuration}>
      <TooltipPrimitive.Root>
        <TooltipPrimitive.Trigger asChild className={className}>
          {children}
        </TooltipPrimitive.Trigger>
        <TooltipPrimitive.Portal>
          <TooltipPrimitive.Content
            side={position}
            sideOffset={8}
            className="px-2 py-1 bg-black text-white text-xs font-mediumrounded shadow-lgz-50animate-in fade-in-0 zoom-in-95 data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=closed]:zoom-out-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
          >
            {content}
            <TooltipPrimitive.Arrow className="fill-black" />
          </TooltipPrimitive.Content>
        </TooltipPrimitive.Portal>
      </TooltipPrimitive.Root>
    </TooltipPrimitive.Provider>
  );
};

export default Tooltip;
