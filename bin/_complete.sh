#!/bin/bash

# Bash completion script for your component management tool
# To use this, save it to a file (e.g., component-completion.bash) and source it:
# source component-completion.bash
# Or add it to your ~/.bashrc or ~/.bash_completion

_component_completions() {
  local cur prev opts
  COMPREPLY=()
  cur="${COMP_WORDS[COMP_CWORD]}"
  prev="${COMP_WORDS[COMP_CWORD-1]}"

  # Available commands
  opts="new merge help"

  # Complete first argument with available commands
  if [ $COMP_CWORD -eq 1 ]; then
    COMPREPLY=( $(compgen -W "${opts}" -- ${cur}) )
    return 0
  fi

  # Complete second argument based on first argument
  case "${prev}" in
    new)
      # No additional arguments needed for 'new' (it prompts interactively)
      COMPREPLY=()
      return 0
      ;;
    merge)
      # No additional arguments for 'merge'
      COMPREPLY=()
      return 0
      ;;
    help)
      # No additional arguments for 'help'
      COMPREPLY=()
      return 0
      ;;
    *)
      COMPREPLY=()
      ;;
  esac
}

# Register the completion function
# Replace 'your-script-name' with the actual name of your script
complete -F _component_completions monorepo

# If your script is called 'component.sh', use:
# complete -F _component_completions component.sh

# Or if you've made it executable and renamed it to just 'component':
# complete -F _component_completions component